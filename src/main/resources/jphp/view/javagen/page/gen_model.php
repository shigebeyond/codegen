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
<form class="layui-form" action="/$javagen/create_model" method="post" style="margin-top: 15px;">
    <div class="layui-form-item">
        <label class="layui-form-label">选择模型</label>
        <div class="layui-input-inline">
            <select name="model" lay-filter="model" lay-verify="required" class="model">
                <option value="">请选择</option>
                <?= render_options($models) ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <button class="layui-btn" lay-submit="" lay-filter="generate">生成代码</button>
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
                '<td><input type="text" name="name" lay-verify="required" placeholder="字段名" class="layui-input"></td>'+
                '<td><select name="type" lay-verify="required" class="field_type">'+
                '<option value="">请选择</option>'+
                option +
                '</select>'+
                '</td>'+
                '<td><input type="text" name="default" value="0" placeholder="默认值" class="layui-input"></td>'+
                '<td><input type="text" name="comment" lay-verify="required" placeholder="字段描述" class="layui-input"></td>'+
                '<td><input type="checkbox" name="is_null" value="NOT NULL" lay-skin="primary" checked="checked"></td>'+
                '<td><input type="checkbox" name="key" value="AUTO_INCREMENT" lay-skin="primary"></td>'+
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
            window.location = '/$javagen/gen_model/'+table_name;
        });

        //监听修改案例提交
        form.on('submit(generate)', function (data) {
            debugger;
            $.ajax({
                url: '/$javagen/create_model',
                //data: data.field,
                data: $('form').serialize(),
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

