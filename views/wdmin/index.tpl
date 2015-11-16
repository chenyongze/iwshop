<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="renderer" content="webkit">
        <title>微点客户管理后台</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="format-detection" content="telephone=no" />
        <link href="{$docroot}favicon.ico" rel="Shortcut Icon" />
        <link href="static/css/wshop_admin_style.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <link href="static/css/wshop_admin_index.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <link href="static/script/fancyBox/source/jquery.fancybox.css" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/fancyBox/source/jquery.fancybox.pack.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/Wdmin/wdmin.js?v={$cssversion}"></script>
    </head>
    <body class="wdmin-main" style="background-image:url('static/images/admin_background/2.jpg');">
        <div id="topnav">
            <div class="in">
                <div class="left">
                    Welcome Back!{*<b>贺维Ivy</b>*} 今天是 {$today}{*，今天有<a href="{$docroot}?/Wdmin/logOut/">7</a>个订单待发货，<a href="{$docroot}?/Wdmin/logOut/">3</a>个新消息。*}
                </div>
                <div class="right clearfix">
                    {include file="./tnav.tpl"}
                </div>
            </div>
        </div>
        <div id="wdmin-wrap">
            <div id="leftNav">{include file="./navs.tpl"}</div>
            <div id="rightWrapper">
                <div id="main-mid">
                    <div id="iframe_loading"></div>
                    <div id="__subnav__"></div>
                    <iframe id="right_iframe" src="" width="100%" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </body>
</html>