<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>会员注册 - {$settings.shopname}</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="format-detection" content="telephone=no">
        <link href="{$docroot}static/css/wshop_companyreg.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="{$docroot}static/script/jquery-2.1.1.min.js?v={$cssversion}"></script>
        <script type="text/javascript" src="{$docroot}static/script/main.js?v={$cssversion}"></script>
        <script type="text/javascript" charset="utf-8" src="{$docroot}static/script/validation/dist/jquery.validate.js"></script>
        <script type="text/javascript" charset="utf-8" src="{$docroot}static/script/validation/dist/lang-cn.js"></script>
    </head>
    <body>        
        <input type="hidden" value="{$smarty.server.HTTP_REFERER}" id="referer" />
        <input type='hidden' value='{$openid}' id='openid' />
        <input type='hidden' value='{$head}' id='head' />
        <input type='hidden' value='{$sex}' id='sex' />
        
        <header class="bheader" id='comHead' style=''><span>代理申请</span></header>
        
    	<!-- from -->
        <form id="login-wrap">
            <header class="Thead">手机号</header>
            <div style="padding:0 10px;">
                <div class="gs-text">
                    <input type="tel" class="required" id="set-form-phone" tabindex="1" placeholder="手机号" />
                </div>
            </div>

            <header class="Thead">真实姓名</header>
            <div style="padding:0 10px;">
                <div class="gs-text">
                    <input type="text" class="required" id="set-form-name" tabindex="2" placeholder="真实姓名" />
                </div>
            </div>

            <header class="Thead">电子邮箱</header>
            <div style="padding:0 10px;">
                <div class="gs-text">
                    <input type="text" class="required" id="set-form-email" tabindex="3" placeholder="电子邮箱" />
                </div>
            </div>

            <header class="Thead">身份证号</header>
            <div style="padding:0 10px;">
                <div class="gs-text">
                    <input type="text" class="required" id="set-form-id" tabindex="4" placeholder="身份证号" />
                </div>
            </div>
        </form>
        
        <a class="button green" href="javascript:;" id="reg-btn">提交申请</a>
        
        {include file="../global/footer.tpl"}
        <script type="text/javascript">
            WeixinJSBridgeReady(ComRegLoad);
        </script>
    </body>
</html>