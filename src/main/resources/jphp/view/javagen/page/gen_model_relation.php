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
<form class="layui-form" action="/$javagen/create_model_relation" method="post" style="margin-top: 15px;">
    <div class="layui-form-item">
        <table class="layui-table" lay-skin="line">
            <thead>
                <tr>
                    <th>主表</th>
                    <th>主键</th>
                    <th>关系</th>
                    <th>从表</th>
                    <th>外键</th>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr>
                    <td>
                        <select name="master_table" lay-verify="required" lay-filter="master_table" class="field_type">
                            <option value="">请选择</option>
                            <?php foreach ($tables as $table){?>
                                <option value="<?=$table?>"><?=$table?></option>
                            <?php }?>
                        </select>
                    </td>
                    <td>
                        <select name="key" lay-verify="required" id="key" class="field_type">
                            <option value="">请选择</option>
                        </select>
                    </td>
                    <td>
                        <select name="relation" lay-verify="required" class="field_type">
                            <option value="">请选择</option>
                            <option value="has_one">has_one</option>
                            <option value="has_many">has_many</option>
                        </select>
                    </td>
                    <td>
                        <select name="slave_table" lay-verify="required" lay-filter="slave_table" class="field_type">
                            <option value="">请选择</option>
                            <?php foreach ($tables as $table){?>
                                <option value="<?=$table?>"><?=$table?></option>
                            <?php }?>
                        </select>
                    </td>
                    <td>
                        <select name="foreign_key" lay-verify="required" id="foreign_key" class="field_type">
                            <option value="">请选择</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
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

        form.render();

        //选择主表，获取主表所有字段名
        form.on('select(master_table)', function (data) {
            get_columns(data.value, $("#key"))
        });
        //选择从表表，获取从表所有字段名
        form.on('select(slave_table)', function (data) {
            get_columns(data.value, $("#foreign_key"))
        });
        //获取字段
        function get_columns(table_name, target) {
            $.ajax({
                url: '/$javagen/columns_json/'+table_name,
                method: 'post',
                dataType: 'json',
                data: {
                    model: table_name
                },
                success: function (res) {
                    if (res.success) {
                        render_columns(res.data, target);
                    } else {
                        layer.msg(res.msg);
                    }
                },
                fail: function (err) {
                    layer.msg(err)
                }
            })
        }
        //重新渲染select
        function render_columns(data, target) {
            target.html('');
            for (var i = 0; i < data.length; i++) {
                var opt = '<option value="' + data[i].name + '">' + data[i].name + '</option>';
                target.append(opt);
            }
            form.render('select')
        }
        //监听修改案例提交
        form.on('submit(generate)', function (data) {
            // $(this).serialize()
            //debugger;
            $.ajax({
                url: '/$javagen/create_model_relation',
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

