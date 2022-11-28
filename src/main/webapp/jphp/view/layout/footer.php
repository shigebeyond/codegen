<script>
    layui.use('form', function() {
        var version = layui.v;
        var form = null;
        if (version.startsWith('2')) {
            form = layui.form;
        } else {
            form = layui.form();
        }
        form.render();
    })
</script>
</body>

</html>