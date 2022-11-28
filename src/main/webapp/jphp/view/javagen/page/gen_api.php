<?php $controller->view('layout/header') ?>
<style>
    .first {
        color: red;
    }
    .layui-form-label {
        width: 120px;
    }
    .clos-class {
        float: left;
    }
    .searchs-class {
        float: left;
    }
    .layui-icon-upload-circle{
        font-size: 20px;
        color: blue;
    }
    .layui-icon-download-circle{
        font-size: 23px;
        color: green;
    }
</style>
<form class="layui-form" action="" id="myform" style="margin-top: 15px;">
    <div class="layui-form-item">
        <label class="layui-form-label first">先选择站点</label>
        <div class="layui-input-inline">
            <select name="site" lay-filter="site" lay-verify="required" class="site">
                <option value="">请选择</option>
                <?= render_options($sites, $site) ?>
            </select>
        </div>
    </div>
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
        <label class="layui-form-label">自定义显示字段</label>
        <div>
            <button type="button" class="layui-btn layui-btn-primary myadd" data-method="add_cols">新增</button>
        </div>
        <div class="layui-input-inline" style="width: 75%;margin-left: 150px;">
            <table class="layui-table" lay-skin="line">
                <thead>
                    <tr>
                        <th>字段</th>
                        <th>中文名</th>
                        <th>列表字段控件</th>
                        <th>关键字搜索字段控件</th>
                        <th>普通搜索字段控件</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="tbody_cols">
                </tbody>
            </table>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="" lay-filter="generate">生成代码</button>
        </div>
    </div>
</form>
<?php $controller->view('layout/footer') ?>

<script>
    layui.use(['form', 'layer', 'upload', 'laydate', 'layedit', 'table', 'laytpl'], function () {
        var $ = layui.jquery,
            layer = layui.layer,
            form = layui.form,
            laydate = layui.laydate,
            table = layui.table,
            laytpl = layui.laytpl,
            layedit = layui.layedit;

        // 日期
        laydate.render({
            elem: 'input[is-date=true]'
        });
        form.render();

        //监听提交
        form.on('select(site)', function (data) {
            var site = data.value;
            var site_name = data.elem[data.elem.selectedIndex].text;
            window.location.href = '/$javagen/gen_api/' + site;
        });

        //监听提交
        form.on('select(model)', function (data) {
            var model = data.value;
            if(model == ''){
                $('#tbody_cols').html('');
                return;
            }
            $.ajax({
                url: '/$javagen/columns_json/' + model,
                data: data.field,
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.code == 0) {
                        var str = '';
                        $.each(data.data, function (id, obj) {
                            str += '<tr>' +
                                '<td><code style="color: rgb(221, 17, 68);">' + obj.name + '</code></td>' +
                                '<td><input type="text" name="labels[' + obj.name + ']" id="labels[' + obj.name + ']" value="' + obj.label + '" placeholder="' + obj.name + '" class="layui-input"></td>' +
                                '<td>' +
                                '<p class="clos-class" style="width: 15%;"><input type="checkbox" name="test1[]" value="true" lay-skin="primary" lay-filter="test1" checked></p>' +
                                '<p class="clos-class" style="width: 80%;"><select name="cols[' + obj.name + ']" id="cols[' + obj.name + ']">' + cols_option() + '</select>' +
                                '</td>' +
                                '<td>' +
                                '<p style="padding-left: inherit;padding-bottom: 10px;"><input type="checkbox" name="keyword_searchs[' + obj.name + ']" value="true" lay-skin="switch" lay-text="是|否" checked></p>' +
                                '</td>' +
                                '<td>' +
                                '<p class="searchs-class" style="width: 15%;"><input type="checkbox" name="test2[]" value="true" lay-skin="primary" lay-filter="test2" checked></p>' +
                                '<p class="searchs-class" style="width: 80%;"><select name="searchs[' + obj.name + ']" id="searchs[' + obj.name + ']">' + searchs_option() + '</select></p>' +
                                '</td>' +
                                '<td><button type="button" class="layui-btn layui-btn-danger layui-btn-sm layui-btn-disabled">删除</button><i class="layui-icon layui-icon-upload-circle"></i><i class="layui-icon layui-icon-download-circle"></i></td>' +
                                '</tr>';
                        });
                        $('#tbody_cols').html(str);
                        $('.clos-class').find('select').val('select');
                        $('.searchs-class').find('select').val('>');
                        form.render();
                    } else {
                        layer.open({
                            type: 0,
                            title: '失败提示',
                            icon: 5,
                            content: data.msg
                        })
                    }
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
        });

        //监听复选按钮
        form.on('checkbox(test1)', function(data){
            var tselect = $(this).parent().next().find('select')
            var Key = tselect.attr('id').match(/(?<=\[)(.+?)(?=\])/g)
            var tlabels=$("input[name='labels["+ Key[0] +"]']")
            if (data.elem.checked) {
                tlabels.attr('name', tlabels.attr('id'))
                tlabels.attr('disabled', false)
                tselect.attr('name', tselect.attr('id'))
                tselect.attr('disabled', false)
                tselect.val('select')
            } else {
                tlabels.attr('name', '')
                tlabels.attr('disabled', true)
                tselect.attr('name', '')
                tselect.attr('disabled', true)
                tselect.val('')
            }
            form.render('select');
        });

        //监听复选按钮
        form.on('checkbox(test2)', function(data){
            var tselect = $(this).parent().next().find('select')
            if (data.elem.checked) {
                tselect.attr('disabled', false)
                tselect.val('>')
            } else {
                tselect.attr('disabled', true)
                tselect.val('')
            }
            form.render('select');
        });

        //监听提交
        form.on('submit(generate)', function (data) {
            console.log('layui表单提交数据=======>')
            console.log(data.field)
            // $(this).serialize()
            //debugger;

            //处理的目的就是把同个下标的数组合并，看上面的打印数据，
            //那些数据提交到服务端后，php底层会自动合并相同下标的数组数据，所有想偷懒的话直接提交一个多维数组过去用php取处理数据
            //这个函数将就者用先，js处理数据太难搞了...
            let [labels, cols, keyword_searchs, searchs] = changeData(data.field);
            let labels_str = '', cols_str = '', searchs_str = '';
            let formField = {};//要提交的数据，有字符串，有数组，所以只定义一维数据
            formField.site = data.field.site;
            formField.model = data.field.model;
            formField.is_tag = data.field.is_tag;

            //处理labels,cols
            var labels_arr = [];
            for (keys in cols) {
                labels_arr.push(keys + ':' + labels[keys]);
                if (cols[keys]) {
                    cols_str += keys + ':' + cols[keys] + ',';
                } else {
                    cols_str += keys + ',';
                }
            }
            if (cols_str) {
                labels_str = labels_arr.join('~');
                cols_str = cols_str.substring(0, cols_str.length - 1);
            }

            //处理keyword_searchs
            for (keys in keyword_searchs) {
                if (keyword_searchs[keys]) {
                    searchs_str += keys + ',';
                }
            }
            if (searchs_str) {
                searchs_str = searchs_str.substring(0, searchs_str.length - 1) + '~';
            }
            //处理searchs
            for (keys in searchs) {
                if (searchs[keys]) {
                    searchs_str += keys + ':' + searchs[keys] + ',';
                }
            }
            searchs_str = searchs_str.substring(0, searchs_str.length - 1);

            formField.labels = labels_str;
            formField.cols = cols_str;
            formField.searchs = searchs_str;

            console.log('经过处理的表单数据=======>')
            console.log(formField)

            $.ajax({
                url: '',
                data: formField,
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

        function changeData(obj) {
            let labels = [], cols = [], keyword_searchs = [], searchs = [];

            for (keys in obj) {
                var labelsExp = /^labels+/;
                if (labelsExp.test(keys)) {
                    var labelsExp2 = /(?<=\[)(.+?)(?=\])/g;
                    var labelsKey = keys.match(labelsExp2)
                    labels[labelsKey[0]] = obj[keys]
                }

                var colsExp = /^cols+/;
                if (colsExp.test(keys)) {
                    var colsExp2 = /(?<=\[)(.+?)(?=\])/g;
                    var colsKey = keys.match(colsExp2)
                    cols[colsKey[0]] = obj[keys]
                }

                var ksExp = /^keyword_searchs+/;
                if (ksExp.test(keys)) {
                    var ksEx2 = /(?<=\[)(.+?)(?=\])/g;
                    var ksKey = keys.match(ksEx2)
                    keyword_searchs[ksKey[0]] = obj[keys]
                }

                var sExp = /^searchs+/;
                if (sExp.test(keys)) {
                    var sEx2 = /(?<=\[)(.+?)(?=\])/g;
                    var sKey = keys.match(sEx2)
                    searchs[sKey[0]] = obj[keys]
                }
            }

            let set = new Set();
            let newObj = {};
            for (const [key, value] of Object.entries(obj)) {
                let idx = key.indexOf("[");
                let idx2 = key.indexOf("]");

                let keyPart1 = key.substring(0, idx);
                let keyPart2 = key.substring(idx + 1, idx2);
                if (!set.has(keyPart1)) {
                    newObj[keyPart1] = [];
                }
                set.add(keyPart1);
                if (idx2 !== key.length - 1) {
                    let idx3 = key.lastIndexOf("[");
                    let idx4 = key.lastIndexOf("]");
                    let keyPart3 = key.substring(idx3 + 1, idx4);

                    if (!newObj[keyPart1][keyPart3]) {
                        newObj[keyPart1][keyPart3] = [];
                    }
                    newObj[keyPart1][keyPart3].push({ [keyPart2]: value });
                } else {
                    newObj[keyPart1].push({ [keyPart2]: value });
                }
            }
            // console.log(newObj)

            return [labels, cols, keyword_searchs, searchs];
        }

        var func = {
            add_cols: function() {
                var obj = {name: 'test', label: '测试'};
                var html = '<tr>' +
                    '<td><input type="text" name="fields[' + obj.name + ']" value="' + obj.name + '" onblur="change_field(this)" class="layui-input"></td>' +
                    '<td><input type="text" name="labels[' + obj.name + ']" value="' + obj.label + '" placeholder="' + obj.name + '" class="layui-input my-labels"></td>' +
                    '<td>' +
                    '<p class="clos-class" style="width: 15%;"><input type="checkbox" name="test1[]" value="true" lay-skin="primary" lay-filter="test1" checked></p>' +
                    '<p class="clos-class my-cols" style="width: 80%;"><select name="cols[' + obj.name + ']" id="cols[' + obj.name + ']">' + cols_option() + '</select>' +
                    '</td>' +
                    '<td>' +
                    '<p class="my-keyword-searchs" style="padding-left: inherit;padding-bottom: 10px;"><input type="checkbox" name="keyword_searchs[' + obj.name + ']" value="true" lay-skin="switch" lay-text="是|否" checked></p>' +
                    '</td>' +
                    '<td>' +
                    '<p class="searchs-class" style="width: 15%;"><input type="checkbox" name="test2[]" value="true" lay-skin="primary" lay-filter="test2" checked></p>' +
                    '<p class="searchs-class my-searchs" style="width: 80%;"><select name="searchs[' + obj.name + ']" id="searchs[' + obj.name + ']">' + searchs_option() + '</select></p>' +
                    '</td>' +
                    '<td><button type="button" class="layui-btn layui-btn-danger layui-btn-sm" onclick="del_div(this)">删除</button><i class="layui-icon layui-icon-upload-circle"></i><i class="layui-icon layui-icon-download-circle"></i></td>' +
                    '</tr>';
                $('#tbody_cols').append(html);
                $('.clos-class:last').find('select').val('select');
                $('.searchs-class:last').find('select').val('>');
                form.render();
            }
        };
        $('.myadd').on('click', function() {
            var othis = $(this), method = othis.data('method');
            func[method] ? func[method].call(this, othis) : '';
        });

        //列表字段控件
        function cols_option() {
            return '<option value=""></option>' +
                '<option value="select">select</option>' +
                '<option value="img">img</option>' +
                '<option value="date">date</option>';
        }

        //普通搜索字段控件
        function searchs_option() {
            return '<option value=""></option>' +
                '<option value=">">></option>' +
                '<option value=">=">>=</option>' +
                '<option value="<"><</option>' +
                '<option value="<="><=</option>' +
                '<option value="like">like</option>' +
                '<option value="date">date</option>' +
                '<option value="date_between">date_between</option>' +
                '<option value="select">select</option>';
        }

        //数据库以外的字段可以修改,所以直接改表单属性
        window.change_field = function(othis) {
            var field = othis.value;
            var trObj = othis.parentNode.parentNode;
            trObj.getElementsByClassName('my-labels')[0].name = 'labels[' + field + ']';

            trObj.getElementsByClassName('my-cols')[0].getElementsByTagName('select')[0].name = 'cols[' + field + ']';
            trObj.getElementsByClassName('my-cols')[0].getElementsByTagName('select')[0].id = 'cols[' + field + ']';

            trObj.getElementsByClassName('my-keyword-searchs')[0].getElementsByTagName('input')[0].name = 'keyword_searchs[' + field + ']';

            trObj.getElementsByClassName('my-searchs')[0].getElementsByTagName('select')[0].name = 'searchs[' + field + ']';
            trObj.getElementsByClassName('my-searchs')[0].getElementsByTagName('select')[0].name = 'searchs[' + field + ']';

            form.render();
        }

        //删除元素
        window.del_div = function(e) {
            e.parentNode.parentNode.remove();
        }
    })
</script>

