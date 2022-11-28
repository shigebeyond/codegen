<?php
use php\jkmvc\orm\Db;
use php\jkmvc\http\HttpRequest;
use php\lang\Reg;
use php\lang\Log;

/**
 * java代码生成器
 *
 */
class Javagen extends BaseController
{
    public function __construct($req, $res){
    {
        parent::__construct($req, $res);
        $this->code_dir = CODEPATH . 'javacode/';
    }

    protected static $type_map = [
        'bigint' => "Long",
        'int' => "Integer",
        'double' => "Double",
        'float' => "Double",
        'double' => "Double",
        'char' => "String",
        'text' => "String",
        'timestamp' => "java.sql.Timestamp",
        'datetime' => "java.sql.Timestamp",
        'date' => "java.sql.Date",
        'time' => "java.sql.Time",
        'binary' => "ByteArray",
        'blob' => "ByteArray",
    ];

    /**
     * 根据db类型，获得java类型
     * @param $db_type
     */
    protected function get_java_type($db_type){
        foreach(static::$type_map as $keyword => $java_type){
            if(strpos($db_type, $keyword) !== false)
                return $java_type;
        }
        return "???";
    }

    /**
     *  生成代码
     * @param $model
     * @param $template 代码模板
     * @param $params 参数
     * @param $file 输出文件
     */
    protected function generate_code($template, $params, $file)
    {
        // 渲染代码模板
        $code = $this->view('javagen/template/'.$template, $params, true);

        // 准备目录
        $file = $this->prepare_file($file, 'kt');

        // 写文件
        file_put_contents($file, $code);

        Log::info("生成代码: $file");
    }

    /**
     * 模型生成——视图
     * @param string $table_name 表名（不含表前缀），如：goods --商品表
     */
    public function gen_model($table_name = ''){
        $data['models'] = $this->list_models();
        $this->view('javagen/page/gen_model', $data);
    }

    /**
     * create_file
     */
    public function create_model()
    {
        $model = $this->req->param('model');
        $this->model($model,'',"model");
    }

    /**
     * 操作入口
     * @param string $site
     */
    public function gen_api($site = ''){
        if ($this->req->isAjax()) {
            $site = $this->req->param('site');
            $model = $this->req->param('model');
            $cols = $this->req->param('cols');
            $searchs = $this->req->param('searchs');

            $this->apicontroller($model, $cols, $searchs);

            $this->json_success();
        }

        $data = $this->req->params();
        $data['site'] = $site;
        $sites = $this->list_sites();
        foreach ($sites as $value) {
            if (strpos($value, 'api') === FALSE) {
                unset($sites[$value]);
            }
        }
        $data['sites'] = $sites;
        $data['models'] = $this->list_models();
        $data['actions'] = $this->get_site_actions($site, $data);
        $this->view('javagen/page/gen_api', $data);
    }

    /**
     * 解析字段: 字段名 + 控件类型/符号 + label中文名
     * @param $cols
     * @param $labels
     * @return array
     */
    function parse_columns($cols, $labels, $model)
    {
        $cols = str_replace(' ', '', $cols);
        if($cols == '')
            return [];

        $all_columns = $this->list_columns($model);
        foreach ($all_columns as &$col)
            $col['type'] = ''; // 去掉字段类型, 后面要改为控件类型
        unset($col);

        $cols = explode(',', $cols);
        $cols2 = [];
        foreach ($cols as $col) {
            if(!Reg::find('^[\*\w\d_]+(:[\w\d_<>!=]+)?$', $col))
                json_error(EXIT_ERROR, "无效表达式: $col");

            // 全部字段
            if($col == '*') {
                $cols2 += $all_columns;
                continue;
            }

            $arr = explode(':', $col);
            $name = $arr[0]; // 字段名
            $type = isset($arr[1]) ? $arr[1] : '';// 控件类型/符号
            $cols2[] = [
                'name' => $name,
                'type' => $type,
                'label' => isset($labels[$name]) ? $labels[$name] : $name,
            ];
        }
        return $cols2;
    }

    public $level = 0;

    /**
     * 生成模型类
     * @param $model string 表名
     * @param $cols string 列表字段名
     *          1 纯字段名, 如 id,name,birthday,sex,state
     * @param $type string 更新model时的操作类型默认是列表更新model
     */
    public function model($model, $cols = '',$type='list')
    {
        if(!$model)
            die("未指定模型名");

        $model = strtolower($model);

        // 获得表标签
        $table_label = $this->get_table_label($model);

        // 获得字段标签
        $column_labels = $this->list_column_labels($model);

        // 解析列表字段
        $cols = $this->parse_columns($cols, $column_labels, $model);
        $model_file = $this->code_dir .'/model/'.$model.'Model.kt';
        $db_cols = $this->list_columns($model);
        foreach($db_cols as &$col){
            $col['java_type'] = $this->get_java_type($col['type']);
        }
        $pk = $this->get_table_pk($model);
        $params = [
            'model' => $model,
            'table_label' => $table_label,
            'columns'=>$cols,
            'db_columns'=>$db_cols,
            'pk' => $pk
        ];
        if(file_exists($model_file)){
            if($type =='list'){
                $cols_code =  $this->view('javagen/template/model_columns', $params, true);
                $content = file_get_contents($model_file);
                $new_model_code = $this->replace_code_fragment($content,$cols_code,'listModel');
                //重新写入
                file_put_contents($model_file,$new_model_code);
            }else{
                $cols_code = '';
            }
        }else{
            $cols_code =  $this->view('javagen/template/model_columns', $params, true);
            $params['cols_code'] = $cols_code;
            $this->generate_code('model', $params, ucfirst($model) . "Model");
        }

        if($this->level == 0)
            $this->json_success();
    }

    /**
     * 生成api控制器类
     * @param $model 模型
     * @param $cols 列表字段名
     *          1 纯字段名, 如 id,name,birthday,sex,state
     * @param $searchs 条件字段
     *          1 纯字段名, 如 id,name,birthday,state
     *          2 字段名+符号, 如 id>=,name:like,birthday:date_between,state:select
     *          3 keyword字段名+普通字段名(见1/2), 两者用~分隔, ~之前为keyword字段名, ~之后为普通字段名, 如 id,name~birthday:date_between,state:select
     */
    public function apicontroller($model, $cols, $searchs = '')
    {
        if(!$model)
            die("未指定模型名");

        $model = strtolower($model);

        // 获得表标签
        $table_label = $this->get_table_label($model);

        // 获得字段标签
        $column_labels = $this->list_column_labels($model);

        // 解析列表字段
        $cols = $this->parse_columns($cols, $column_labels, $model);
        // 解析搜索字段
        // keyword字段名+普通字段名
        $arr = explode('~', $searchs, 2);
        $keywords = '';
        if(count($arr) == 2){
            list($keywords, $searchs) = $arr;
        }
        $keywords = $this->parse_columns($keywords, $column_labels, $model);
        $searchs = $this->parse_columns($searchs, $column_labels, $model);

        $template = 'api-controllers';
        $file = ucfirst($model).'Controller';
        $params = [
            'model' => $model,
            'table_label' => $table_label,
            'columns' => $cols,
            'keywords' => $keywords,
            'searchs' => $searchs,
        ];
        $this->generate_code($template, $params, $file);

        $this->json_success();
    }

    /**
     * 模型关联关系生成
     */
    public function gen_model_relation()
    {
        $dbname = $this->db->getSchema();
        $dbprefix = $table = $this->db->getTablePrefix();
        $sql = 'SHOW TABLES';
        $tables = $this->db->query($sql);
        $tables = array_column($tables,'Tables_in_'.$dbname);
        foreach ($tables as &$table){
            $table = str_replace($dbprefix,'',$table);
        }
        $data = [];
        $data['tables'] = $tables;
        $this->view('javagen/page/gen_model_relation', $data);
    }

    /**
     * 模型关联关系生成
     */
    public function create_model_relation()
    {
        /**
         * [master_table] => admin_group
         * [key] => id
         * [relation] => has_many
         * [slave_table] => admin_group
         * [foreign_key] => id
         */
        extract($this->req->params());
        if($master_table==$slave_table)
            $this->show_error('主从不能一致');
        /***
         * 主表model
         * */
        $path = $this->code_dir . '/model/'.ucfirst($master_table) . 'Model.kt';
        //获取内容
        if (file_exists($path)) {
            $file_content = file_get_contents($path);
        }else{
            $this->show_error('不存在该model-'.$path);
        }
        //编辑主关系
        $put_string = $this->edit_relation_code($file_content,$this->req->params());
        file_put_contents($path,$put_string);

        /***
         * 从表model
         * */
        $path = $this->code_dir . '/model/'.ucfirst($slave_table) . 'Model.kt';
        //获取内容
        if (file_exists($path)) {
            $file_content = file_get_contents($path);
            $post_data = [
                'slave_table' => $master_table,
                'foreign_key' => $foreign_key,
                'key' => $key,
                'relation' => 'belongs_to',
            ];
            $put_string = $this->edit_relation_code($file_content,$post_data);
            file_put_contents($path,$put_string);
        }
        $this->json_success();
    }

    /**
     * @param $content
     * @param $post_data
     */
    public function edit_relation_code($file_content, $post_data)
    {
        extract($post_data);
        //是否存在数据关系 (不存在)
        if (!preg_match('protected\s+\$'.$relation.'\s+\=',$file_content)) {
            //在key 或者 table 后面添加字符串
            if(preg_match_all('public\s+\$(key|table)\s+\=\s+[\'|\"][0-9a-zA-Z_]+[\'|\"];', $file_content,$preg_data)){
                $search_content = end($preg_data[0]);
                //判断是否存在
                if (empty($search_content)) {
                    $this->show_error('查找追加的内容');
                }
                $post_data = [
                    'relation' => $relation,
                    'list' => [
                        [
                            'slave_table' => $slave_table,
                            'key' => $key,
                            'foreign_key' => $foreign_key
                        ]
                    ]
                ];
                $put_str = $this->view('javagen/template/relation_model', $post_data, TRUE);
                $file_content = str_replace($search_content,$search_content.PHP_EOL.'    '.$put_str,$file_content);
            }
            //在table_name后面添加字符串
        }else{
            $add_s = $relation == 'has_many'?'s':'';
            $preg_string = '\''.$slave_table.$add_s.'\'\s+\=>\s+array\(\s+(.*?)\s+\'model\'\s+\=> \''.$slave_table.'_model\', (.*?)\s+\'foreign_key\'\s+\=>\s+\''.$foreign_key.'\',\s+(.*?)\s+\'key\'\s+\=> \''.$key.'\'(.*?)\s+\s+\)';
            //判断内容是否存在（不存在则）
            if(!preg_match($preg_string,$file_content)){
                $pattern = 'protected\s+\$'.$relation.'\s+\=\s+array\(\s+(.*?)\s+\);';
                preg_match_all($pattern,$file_content,$preg_string);
                $put_string = " 
       '".$slave_table.$add_s."' => array(      // 别名，用于访问关联模型
             'model' => '".$slave_table."_model', // 模型：拥有哪个模型
             'foreign_key' => '".$foreign_key."', // 外键
             'key' => '".$key."'
            ),";
                $file_content = str_replace($preg_string[1][0],$preg_string[1][0].'    '.$put_string.PHP_EOL,$file_content);
            }
        }
        return $file_content;
    }

}