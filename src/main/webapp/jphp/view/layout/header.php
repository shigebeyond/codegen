<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>代码生成器</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?= $domain_static ?>layui/css/layui.css" media="all"/>
    <script src="<?= $domain_static ?>jquery/jquery.min.js"></script>
    <script src="<?= $domain_static ?>layui/layui.js"></script>
    <script src="<?= $domain_static ?>common/helper.js"></script>
</head>
<style>
    .panel-padding {
        padding: 20px 10px;
    }
    .panel {
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid transparent;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.05);
        box-shadow: 0 1px 1px rgba(0,0,0,0.05);
    }
    .layui-table-tool-self {
        display: none;
    }
</style>
<body>