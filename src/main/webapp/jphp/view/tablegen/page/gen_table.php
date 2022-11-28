
<?php $controller->view('layout/header') ?>
<style>
    .layui-icon-upload-circle{
        font-size: 20px;
        color: blue;
    }
    .layui-icon-download-circle{
        font-size: 23px;
        color: green;
    }
</style>
<div class="layui-tab-content">
    <form class="layui-form" action="/$tablegen/create_table" method="post" style="margin-top: 15px;">
        <div class="layui-form-item">
            <h2>1 指定表信息</h2/>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">表名</label>
            <div class="layui-input-inline">
                <input type="text" name="table_name" lay-verify="required" placeholder="表名" class="layui-input" id="table_name" value="<?=$table_name?>">
            </div>
            <button type="button" class="layui-btn" style="height: 38px;" id="check_tablename">检测表名是否存在</button>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">表描述</label>
            <div class="layui-input-inline" style="width: 380px;">
                <input type="text" name="table_comment" lay-verify="required" placeholder="表描述" class="layui-input" value="<?=$table_comment?>">
            </div>
        </div>
        <div class="layui-form-item">
            <h2>2 指定字段列表</h2/>
        </div>
        <div class="layui-form-label">
            <button type="button" class="layui-btn layui-btn-primary" id="add_btn">新增字段</button>
        </div>
        <div class="layui-form-item">
            <table class="layui-table" lay-skin="line">
                <thead>
                <tr>
                    <th>字段名</th>
                    <th>字段类型</th>
                    <th>默认值</th>
                    <th>字段描述</th>
                    <th>非空</th>
                    <th>主键</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="tbody">
                <?php if($columns){?>
                    <?php foreach ($columns as $key=>$column){?>
                        <tr>
                            <td><input type="text" name="fields[<?=$key?>][name]" value="<?=$column['name']?>" lay-verify="required" placeholder="字段名" readonly class="layui-input"></td>
                            <td><input type="text" name="fields[<?=$key?>][type]" value="<?=$column['type']?>" lay-verify="required" placeholder="字段类型" class="layui-input"></td>
                            <td><input type="text" name="fields[<?=$key?>][default]" value="<?=$column['default']?>"  placeholder="默认值" class="layui-input"></td>
                            <td><input type="text" name="fields[<?=$key?>][comment]" value="<?=$column['label']?>" lay-verify="required" placeholder="字段描述" class="layui-input"></td>
                            <td><input type="checkbox" name="fields[<?=$key?>][is_null]" value="NOT NULL" lay-skin="primary" <?php if($column['is_null']=='NO'){?>checked="checked"<?php }?>></td>
                            <td><input type="checkbox" name="fields[<?=$key?>][key]" value="AUTO_INCREMENT" lay-skin="primary" <?php if($column['key']=='PRI'){?>checked="checked"<?php }else{?> disabled <?php }?> ></td>
                            <td><button type="button" class="layui-btn layui-btn-danger layui-btn-sm">删除</button><i class="layui-icon layui-icon-upload-circle"></i><i class="layui-icon layui-icon-download-circle"></i></td>
                        </tr>
                    <?php }?>
                <?php }else{?>
                    <tr>
                        <td><input type="text" name="fields[0][name]" lay-verify="required" placeholder="字段名" class="layui-input"></td>
                        <td>
                            <select name="fields[0][type]" lay-verify="required" class="field_type">
                                <option value="">请选择</option>
                                <?= render_options($data_type) ?>
                            </select>
                        </td>
                        <td><input type="text" name="fields[0][default]" value="0" placeholder="默认值" class="layui-input"></td>
                        <td><input type="text" name="fields[0][comment]" lay-verify="required" placeholder="字段描述" class="layui-input"></td>
                        <td><input type="checkbox" name="fields[0][is_null]" value="NOT NULL" lay-skin="primary" checked="checked"></td>
                        <td><input type="checkbox" name="fields[0][key]" value="AUTO_INCREMENT" lay-skin="primary"></td>
                        <td><button type="button" class="layui-btn layui-btn-danger layui-btn-sm">删除</button><i class="layui-icon layui-icon-upload-circle"></i><i class="layui-icon layui-icon-download-circle"></i></td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn" lay-submit="" lay-filter="generate">生成表</button>
        </div>
    </form>
</div>
<?php $controller->view('layout/footer') ?>

<script>
    layui.use(['form', 'layer', 'upload', 'laydate', 'layedit', 'table', 'laytpl'], function () {
        var $ = layui.jquery,
            layer = layui.layer; //独立版的layer无需执行这一句

        var layedit = layui.layedit;
        var table = layui.table;
        var form = layui.form;
        var laydate = layui.laydate;
        var laytpl = layui.laytpl;
        var myaql_data_type = <?=json_encode(array_values($data_type))?>;
        var option = '';
        for (var i = 0; i < myaql_data_type.length; i++) {
            option += '<option value="' + myaql_data_type[i] + '">' + myaql_data_type[i] + '</option>';
        }
        form.render();
        //新增字段
        $('#add_btn').on('click',function (){
            var count = $('#tbody').children().length;
            var html = '<tr>'+
                '<td><input type="text" name="fields['+count+'][name]" lay-verify="required" placeholder="字段名" class="layui-input"></td>'+
                '<td><select name="fields['+count+'][type]" lay-verify="required" class="field_type">'+
                '<option value="">请选择</option>'+
                option +
                '</select>'+
                '</td>'+
                '<td><input type="text" name="fields['+count+'][default]" value="0" placeholder="默认值" class="layui-input"></td>'+
                '<td><input type="text" name="fields['+count+'][comment]" lay-verify="required" placeholder="字段描述" class="layui-input"></td>'+
                '<td><input type="checkbox" name="fields['+count+'][is_null]" value="NOT NULL" lay-skin="primary" checked="checked"></td>'+
                '<td><input type="checkbox" name="fields['+count+'][key]" value="AUTO_INCREMENT" lay-skin="primary"></td>'+
                '<td><button type="button" class="layui-btn layui-btn-danger layui-btn-sm">删除</button><i class="layui-icon layui-icon-upload-circle"></i><i class="layui-icon layui-icon-download-circle"></i></td>'+
                '</tr>';
            $('#tbody').append(html);
            $('.layui-btn-sm').on('click',function () {//重新绑定点击事件
                $(this).parent().parent().remove();
            });
            form.render();//重新渲染
        });

        //删除字段
        $('.layui-btn-sm').on('click',function () {
            $(this).parent().parent().remove();
        });

        //检测表名是否存在
        $('#check_tablename').on('click',function (){
            var table_name = $('#table_name').val();
            window.location = '/$tablegen/codegen_table/'+table_name;
        });

        //监听修改案例提交
        form.on('submit(generate)', function (data) {
            //debugger;
            $.ajax({
                url: '/$tablegen/create_table',
                //data: $('form').serialize(),
                data: data.field,
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    layer.msg(data.msg, {
                        time: 10000,
                        icon:6,
                        content: data.msg || '生成成功'
                    });
                },
                error: function () {
                    layer.open({
                        type: 0,
                        title: '网络错误',
                        icon: 5,
                        content: '网络超时！'
                    })
                }
            });
            return false;
        });
    })
</script>

