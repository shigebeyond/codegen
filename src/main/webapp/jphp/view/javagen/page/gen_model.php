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

        //监听修改案例提交
        form.on('submit(generate)', function (data) {
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

