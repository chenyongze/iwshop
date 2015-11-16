<script type="text/javascript">
    if (!+[1, ]) {
        document.execCommand("Stop");
        location.href = '/html/noIe/';
    }
</script>
<!DOCTYPE html>
<html>
    <head>
        <title>微店后台管理登录</title>
        <meta charset="utf-8" />
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="format-detection" content="telephone=no">
        <link href="{$docroot}favicon.ico" rel="Shortcut Icon" />
        <link href="static/css/wshop_admin_login.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
    </head>
    <body class="loginBody" style="background-image:url('static/images/admin_background/{$rand}.jpg');">
        <div id="login" class="clearfix">
            <div class="login-form" id="login-frame">
                <div id="loading" style="display:none;"></div>
                {if $settings.admin_setting_icon neq ''}
                    <img src="{$docroot}uploads/banner/{$settings.admin_setting_icon}" height="100px" width="100px" />
                {elseif $settings.admin_setting_icon eq ''}
                    <img src="static/images/login/profle_1.png" height="100px" width="100px" />
                {/if}
                <p> &nbsp; </p>
                <div class='login-item'>           
                    <div class="gs-text">
                        <input type="text" tabindex="1" value="{$smarty.cookies.admin_acc}" name="username" id="pd-form-username" placeholder="用户名"/>
                    </div>
                </div>
                <div class='login-item'>     
                    <div class="gs-text">
                        <input type="password" tabindex="2" name="password" id="pd-form-password" placeholder="密码" />
                    </div>
                </div>
                <div class='login-item'>  
                    <a class="login-gbtn" href="javascript:;">登录</a>
                </div>
                <div id="copyrights">&COPY; 2014-2015 iWshop All rights reserved.</div>
            </div>
        </div>
        <script type="text/javascript" src="static/script/jquery-2.1.1.min.js?v={$cssversion}"></script>
        <script type="text/javascript" src="static/script/spin.min.js?v={$cssversion}"></script>
        <script type="text/javascript" src="static/script/Wdmin/wdmin.js?v={$cssversion}"></script>
        <script type="text/javascript">
            var loading = false;
            $(function () {
                if (parent !== undefined && parent.location.href !== location.href) {
                    parent.location.href = location.href;
                }
                if ($('#pd-form-username').val() === '') {
                    $('#pd-form-username').focus();
                } else {
                    $('#pd-form-password').focus();
                }
                // 登录按钮点击
                $('.login-gbtn').click(loginCheck);
                // 密码输入框回车
                $('#pd-form-password').keyup(function (e) {
                    if (e.keyCode === 13) {
                        $('.login-gbtn').click();
                    }
                });
                $(window).bind('resize', function () {
                    $('#login').css('padding-top', (document.body.clientHeight - $('#login').height() - 15) / 2);
                }).resize();
                $('.login-form').addClass('loginShow');
            });
        </script>
    </body>
</html>