<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>代理申请 - {$settings.shopname}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="format-detection" content="telephone=no" />
        <link href="static/css/wshop_company_request.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
    </head>
    <body>

        <header class="bheader" id='comHead' style=''><span>代理申请</span></header>

        <section class='companySig'>
            <div class='sec{if !$user_buyed} disable{/if}' data-type="0">
                <i class='check'></i>
                普通代理<br />只要有一次成功购物记录，即可申请。<br />普通代理返佣 : 订单金额的<b>2%</b>
            </div>
            <div class='sec{if $user_ordersum < 2000} disable{/if}' data-type="1">
                <i class='check'></i>
                晋级代理<br />累计消费达到2000元即可升级或申请为晋级代理。<br />晋级代理返佣 : 订单金额的<b>3.5%</b>
            </div>
            <div class='sec' data-type="2">
                <i class='check'></i>
                专业代理<br />提供相关个人信息、通过审核后即可成为专业代理。<br />专业代理返佣 : 
                <br />订单金额的<b>5%</b> + 二级团队订单金额<b>1%</b>
            </div>
        </section>

        <!-- 微信支付按钮 -->
        <a class="button green disable" id="confirm" href="#">同意所选协议并申请</a>

        {include file="../global/footer.tpl"}
        <script data-main="static/script/Wshop/shop_companyreg.js?v={$cssversion}" src="static/script/require.min.js"></script>
    </body>
</html>