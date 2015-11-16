<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>会员注册 - {$settings.shopname}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="format-detection" content="telephone=no">
        <link href="{$docroot}static/css/wshop_uc.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="{$docroot}static/script/jquery-2.1.1.min.js?v={$cssversion}"></script>
        <script type="text/javascript" src="{$docroot}static/script/main.js?v={$cssversion}"></script>
        <script type="text/javascript" charset="utf-8" src="{$docroot}static/script/validation/dist/jquery.validate.js"></script>
        <script type="text/javascript" charset="utf-8" src="{$docroot}static/script/validation/dist/lang-cn.js"></script>
    </head>
    <body>        
        <input type="hidden" value="{$smarty.server.HTTP_REFERER}" id="referer" />
        <p class="login-tip">用户注册</p>
        <form id="login-wrap">
            <input type='hidden' name="client_wechat_openid" value='{$openid}' id="openid" />
            <input type='hidden' name="client_head" value='{$head}' />
            <input type='hidden' name="client_sex" value='{$sex}' />
            <input type='hidden' name="client_province" value='{$province}' />
            <input type='hidden' name="client_city" value='{$city}' />
            <input type='hidden' name="client_address" value='{$province}{$city}' />
            <input type='hidden' name="client_nickname" value='{$nickname}' />
            <input type='hidden' value='{$smarty.now|date_format:'%Y-%m-%d'}' name='client_joindate' />
            <p class="field-ttip">姓名：</p>
            <div class="login-field clearfix">
                <i class="login-icon name"></i>
                <div class="login-input">
                    <input type="text" name="client_name" class="required" tabindex="3" placeholder="姓名" />
                </div>
            </div>
            <p class="field-ttip">手机号：</p>
            <div class="login-field clearfix">
                <i class="login-icon-account"></i>
                <div class="login-input">
                    <input type="tel" class="required" name="client_phone" id="pho" tabindex="1" placeholder="手机号" />
                </div>
            </div>
            <p class="field-ttip">邮箱：</p>
            <div class="login-field clearfix">
                <i class="login-icon email"></i>
                <div class="login-input">
                    <input type="email" name="client_email" class="required" tabindex="2" placeholder="邮箱" />
                </div>
            </div>
        </form>
        <div id="login-com-wrap">
            <a class="button green" href="javascript:;" id="reg-btn">马上注册</a><br />
            <a class="reg-tip" href="{$docroot}?/Uc/login/">已经有账号了？马上登陆</a>
        </div>
        {include file="../global/footer.tpl"}
        <script type="text/javascript">
            $(function(){
                UcRegLoad();
            });
        </script>
    </body>
</html>