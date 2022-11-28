<?php
use php\jkmvc\http\IController;
use php\jkmvc\orm\Db;
use php\jkmvc\http\HttpRequest;
use php\lang\Log;
use php\lang\Reg;

class BaseController extends IController{

    public function __construct($req, $res){
    {
        parent::__construct($req, $res);
        $this->db = Db::instance("default");
        $this->gen_file = NULL;
    }

    protected function json_success($msg = NULL, $data = NULL){
        if(!$msg){
            if($this->gen_file)
                $msg = "生成文件成功: {$this->gen_file}";
            else
                $msg = '操作成功';
        }
        $this->res->renderJson(0, $msg, $data);
        die();
    }

    protected function show_error($msg){
        $this->res->renderJson(1, $msg);
        die();
    }

    /**
     * 渲染视图：添加域名
     * @param $file
     * @param null $data
     * @param false $is_return
     * @return mixed
     */
    public function view($file, $data = NULL, $is_return = FALSE){
        $domains = ['domain_static' => 'http://static.jym0.com/'];
        if($data === NULL)
            $data = $domains;
        else
            $data += $domains;
        return parent::view($file, $data, $is_return);
    }

    /**
     * 列出字段
     * @param $model
     * @return mixed
     */
    protected function list_columns($model)
    {
        $table = $this->db->getTablePrefix() . $model;
        $sql = "SELECT COLUMN_NAME, COLUMN_DEFAULT, IS_NULLABLE, COLUMN_TYPE, COLUMN_COMMENT,DATA_TYPE,COLUMN_KEY FROM Information_schema.columns WHERE table_name = ? and table_schema = ?";
        $cols = $this->db->query($sql, [$table, $this->db->getSchema()]);
        $ret = [];
        foreach ($cols as $col){
            $item = [
                'name' => $col['COLUMN_NAME'],
                'type' => $col['COLUMN_TYPE'],
                'label' => $col['COLUMN_COMMENT'],
                'is_null' => $col['IS_NULLABLE'],
                'key' => $col['COLUMN_KEY'],
                'default' => $col['COLUMN_DEFAULT']
            ];
            $ret [] = $item;
        }
        return $ret;
    }

    /**
     * 列出字段标签
     * @param $model
     * @return 字段名对字段注释的映射
     */
    protected function list_column_labels($model){
        //获取表单传过来的labels字符串：name:测试~age:18，或数组
        //然后把它转化成和原来一样：['字段名1' => '字段注释1', '字段名2' => '字段注释2']
        $labels = $this->req->params2Object('labels');
        if ($labels) {
            if (is_array($labels))
                return $labels;

            $result = [];
            $labels_arr = explode('~', $labels);
            foreach ($labels_arr as $value) {
                $array = explode(':', $value, 2);
                $result[$array[0]] = $array[1];
            }
            return $result;
        }
        //表单为空查数据库取所有字段
        $cols = $this->list_columns($model);
        $result = array_column($cols, 'label', 'name');
        foreach($result as $name => &$label){
            if(trim($label) == '')
                $label = $name;
        }
        return $result;
    }

    /**
     * 获得表主键
     */
    protected function get_table_pk($table){
        $cols = $this->list_columns($table);
        foreach($cols as $col){
            if($col['key'] == 'PRI')
                return $col["name"];
        }
        return "";
    }

    /**
     * 获得表标签
     * @param $model
     * @return string 表的注释
     */
    protected function get_table_label($model){
        $table = $this->db->getTablePrefix() . $model;
        $sql = "SELECT * FROM information_schema.tables WHERE table_name = ? and table_schema = ?";
        $r = $this->db->query($sql, [$table, $this->db->getSchema()]);
        if(!$r)
            return '';
        $r = $r[0]['TABLE_COMMENT'];
        //return trim($r, '表'); // 第二个参数是charlist, 中文字符会被当成2个char, 导致截断有问题导致乱码
        return Reg::replace('表$', '', $r);
    }

    /**
     * 列出模型
     * @return array
     */
    protected function list_models(){
        $sql = "SELECT * FROM information_schema.tables WHERE table_schema = ?";
        $rows = $this->db->query($sql, [$this->db->getSchema()]);
        $result = [];
        foreach ($rows as $row){
            $model = str_replace($this->db->getTablePrefix(), '', $row['TABLE_NAME']);
            $label = Reg::replace('表$', '', $row['TABLE_COMMENT']);
            $result[$model] = $model.' - '.$label;
        }
        return $result;
    }

    /**
     * 列出站点
     * @return array
     */
    protected function list_sites(){
        return ['pc.localhost', 'api.localhost'];
    }

    /**
     * 获得站点类型
     * @param $site
     * @return string
     */
    protected function get_site_type($site){
        if(end_with($site, 'api'))
            return 'api';
        return 'pc';
    }

    /**
     * 获得站点的动作
     * @param $site
     * @param $data
     * @return mixed
     */
    protected function get_site_actions($site, $data)
    {
        $site2actions = [
            'pc' => ['mvc', 'model', 'controller', 'list_view', 'tag_list_view', 'detail_view', 'form_view'],
            'api' => ['apicontroller'],
        ];
        if ($site) {
            $type = $this->get_site_type($site);
            $actions = $site2actions[$type];
            return array_combine($actions, $actions);
        }

        return [];
    }

    /**
     * 获取mysql数据类型
     * @return array|false
     */
    protected function get_mysql_datatypes(){
//        $sql = 'SELECT data_type FROM information_schema.COLUMNS GROUP BY DATA_TYPE';
//        $types = $this->db->query($sql);
//        $data_type = array_column($types,'data_type','data_type');

        //默认写死这些常用的mysql类型
        $data_type = array(
            'int(10) unsigned'=>'int(10) unsigned',
            'bigint(20) unsigned'=>'bigint(20) unsigned',
            'tinyint(3) unsigned'=>'tinyint(3) unsigned',
            'varchar(255)'=>'varchar(255)',
            'char(100)'=>'char(100)',
            'decimal(10,2) unsigned'=>'decimal(10,2) unsigned',
            'float(5,2) unsigned'=>'',
            'text'=>'text'
        );
        return $data_type;
    }

    /**
     * 获得列json数据
     * @param $model
     */
    public function columns_json($model){
        $this->json_success('success', $this->list_columns($model));
    }

    /**
     * @param string $file
     * @param string $suffix
     * @return string
     */
    function prepare_file(string $file, string $suffix): string
    {
        $root = $dir = $this->code_dir;
        // 准备好目录
        $subdir = substr_before_last($file, '/');
        if ($subdir !== false)
            $dir = $dir . $subdir . '/';
        if (!is_dir($dir))
            mkdir($dir, 0775, TRUE);

        // 拼接文件路径
        $this->gen_file = $root . $file . '.' . $suffix;
        return $this->gen_file;
    }

    /**
     * 压缩文件
     * @param string|array $files 文件名称，支持字符串或数组
     * @param string $name 压缩后的名称
     * @param bool $is_del 压缩后是否删除原文件
     */
    function compress($files = [], $name = 'xxx', $is_del = false){

    }

}