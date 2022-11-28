<?php
use php\jkmvc\orm\Db;
use php\jkmvc\http\HttpRequest;
use php\lang\Reg;
use php\lang\Log;

/**
 * 表生成器
 *
 */
class Tablegen extends BaseController
{
    public function __construct($req, $res){
    {
        parent::__construct($req, $res);
        $this->code_dir = CODEPATH . 'tablecode/';
    }

    /**
     * 表生成——视图
     * @param string $table_name 表名（不含表前缀），如：goods --商品表
     */
    public function gen_table($table_name = ''){
        $data['table_name'] = $table_name;
        $data['table_comment'] = $this->get_table_label($table_name);
        $data['columns'] = $this->list_columns($table_name);
        $data['data_type'] = $this->get_mysql_datatypes($table_name);
        $this->view('tablegen/page/gen_table', $data);
    }


    /**
     * create_file
     */
    public function create_table()
    {
        $table_name = $this->req->param('table_name');
        //数据库操作
        $db_fields = $this->list_columns($table_name);

        //对比字段并且生成sql语句
        $fields = $this->req->params2Object("fields");
        foreach ($fields as &$val) {
            $val['is_null_orgin'] = isset($val['is_null'])?'NO':'YES';
            $val['is_null'] = $val['is_null']??'';
            $val['default_orgin'] =  $val['default'] ;
            $val['default'] = isset($val['key'])?'':(empty($val['default'])?'':"DEFAULT '".$val['default']."' ");//存在key没有默认值
            $val['comment_orgin'] =$val['comment'];
            $val['comment'] = "COMMENT '" . $val['comment'] . "' ";
        }
        $data = [
            'table_name' => $table_name, //数据表
            'table_comment' => $this->req->param('table_comment'), //数据表备注
            'fields' => $fields
        ];
        //数据库不存在表则创建表
        $sql = '';
        if(empty($db_fields)){
            $sql =  $this->view('tablegen/template/create_table', $data, true);
        } else {
            $data['db_fields'] = $db_fields;
            $sql =  $this->view('tablegen/template/alter_table', $data, true);
        }
        //检查sql语句规范
        if ($db_fields && (strstr($sql, 'drop') || strstr($sql, 'create'))) {
            $this->show_error('已经存在该表');
        }
        if($sql)
            $this->db->execute($sql);

        $this->json_success();
    }

}