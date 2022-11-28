<?php $controller->view('layout/header') ?>
<style>
    .layui-colla-item {
        position: relative;
    }
    .download {
        position: absolute;
        top: 10px;
        right: 15px;
    }
    .copy {
        cursor:pointer;
    }
</style>
<fieldset class="layui-elem-field layui-field-title"><legend>生成的代码都在下面</legend></fieldset>
<div class="layui-collapse" lay-filter="view-vue">
    <?php foreach ($files as $key => $value) { ?>
    <div class="layui-colla-item">
        <h2 class="layui-colla-title"> <?= $value['title'] ?></h2>
        <a href="javascript:;" class="download" onclick="downLoad('<?= $value['title'] ?>')">
            <i class="layui-icon layui-icon-download-circle" style="font-size: 20px;"></i>
        </a>
        <div class="layui-colla-content">
            <pre class="layui-code"><?= htmlspecialchars($value['content']) ?></pre>
        </div>
    </div>
    <?php } ?>
</div>
<iframe id="iframe" src="" style="display: none"></iframe>
<?php $controller->view('layout/footer') ?>
<script>
    layui.use(['element', 'code', 'layer'], function() {
        var element = layui.element;
        var layer = layui.layer;

        layui.code({
            title: '<a href="javascript:;" class="copy" title="复制"><i class="layui-icon layui-icon-templeate-1"></i></a>'
            , height: '350px'
            , encode: true
            , about: false
        });

        window.downLoad = function(path) {
            layer.confirm('确定下载?', {
                title:'下载',
                btn: ['确认','取消'] //按钮
            },function(index) {
                $('#iframe').attr('src', '/$home/download?path=' + path);
                layer.close(index);
            });
        }
    });
</script>
