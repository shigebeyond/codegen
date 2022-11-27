<?php
/**
 * 渲染单个字段
 * @param $name
 * @param $type 控件类型, 也可认为是操作符
 * @return
 */
function render_column_condition($name, $type){
    if($type == 'date_between')
        return "$name date_between :{$name}1,:{$name}2";

    $op = $type;
    if($type == 'select' || $op == 'date')
        $op = '';

    if($op == '')
        return $name;

    return "$name $op";
}
?>
<\?php

/**
* <?=$table_label?> API控制器
*/
class <?=ucfirst($model)?> extends Api_base
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('<?=$model?>_model');
    }

    /**
     * 列表页
     */
    public function <?=$model?>_list()
    {
        $data = [];

        // 过滤多个简单的字段值
        $where = $this->build_where(
<?php foreach ($searchs as $col){ // 普通字段
    $name = $col['name'];
    $type = $col['type'];
    $cond = render_column_condition($name, $type);
    $delimiter = end($searchs) == $col ? '' : ',';
    echo "\t\t\t\t'$model.$cond'$delimiter\n";
} ?>
        );

    <?php if ($keywords){ // 关键字字段 ?>
        // 过滤 keyword_type 指定的某个字段值(关键字)
        $where += $this->build_keyword_condition('keyword_type', 'keyword', array(
            <?php foreach ($keywords as $col){
                $name = $col['name'];
                $type = $col['type'];
                ?>
            <?=render_column_condition($name, $type)?>,
            <?php } ?>
        ));
    <?php } ?>
        // 解析分页参数: 会自动将 page + page_size + offset 参数填充到 $data 中
        $this->parse_page($data);

        // 查询总数与分页数据
        $data += $this-><?=$model?>_model->where($where)->count_and_list($data['page_size'], $data['offset']);

        json_success(EXIT_SUCCESS, '<?=$table_label?>列表获取成功', 'success', $data);
    }

    /**
     * 新建页
     */
    public function create_<?=$model?>()
    {
        if($controller->req->isAjax())
            json_error(EXIT_ERROR, '非post请求', 'alert', []);

  <?php foreach ($columns as $col){
          $name = $col['name'];
          $label = $col['label'];
          $type = $col['type'];
      ?>
        $form_data['<?= $name ?>'] = post('<?= $name ?>', '<?= $name ?>', 'var_trim|required', 'success', EXIT_ERROR);
      <?php if($type == 'date' || $type == 'date_between'){?>
        $form_data['<?= $name ?>'] = strtotime($form_data['<?= $name ?>']);
  <?php }} ?>
        $id = $this-><?=$model?>_model->insert($form_data);
        if (!$id) {
            json_error(EXIT_ERROR, '保存失败！', 'success', []);
        }
        json_success(EXIT_SUCCESS, '保存成功！', 'success', ['id' => $id]);
    }

    /**
     * 详情页
     */
    public function <?=$model?>_detail()
    {
        $id = get('id', 'id', 'var_trim|required', 'success', EXIT_ERROR);
        $data = $this-><?=$model?>_model->find(intval($id));
        json_success(EXIT_SUCCESS, '详情获得成功', 'success', $data);
    }

    /**
     * 编辑页+提交
     */
    public function edit_<?=$model?>()
    {
        if($controller->req->isAjax())
            json_error(EXIT_ERROR, '非post请求', 'alert', []);

        $id = post('id', 'id', 'var_trim|required', 'success', EXIT_ERROR);

        // post编辑提交
  <?php foreach ($columns as $col){
          $name = $col['name'];
          $label = $col['label'];
          $type = $col['type'];
      ?>
        $form_data['<?= $name ?>'] = post('<?= $name ?>', '<?= $name ?>', 'var_trim', 'success', EXIT_ERROR);
      <?php if($type == 'date' || $type == 'date_between'){?>
        $form_data['<?= $name ?>'] = strtotime($form_data['<?= $name ?>']);
    <?php }} ?>
        $result = $this-><?=$model?>_model->update($form_data, intval($id));
        if (!$result) {
           json_error(EXIT_ERROR, '更新失败！', 'success', []);
        }
        json_success(EXIT_SUCCESS, '更新成功！', 'success', []);
    }


    /**
     * 删除指定资源
     */
    public function delete_<?=$model?>()
    {
        if($controller->req->isAjax())
            json_error(EXIT_ERROR, '非post请求', 'alert', []);

        $id = post('id', 'id', 'var_trim|required', 'success', EXIT_ERROR);
        $result = $this-><?=$model?>_model->delete(intval($id));
        if (!$result) {
            json_error(EXIT_ERROR, '删除失败！', 'success', []);
        }
        json_success(EXIT_SUCCESS, '删除成功！', 'success', []);
    }
}
