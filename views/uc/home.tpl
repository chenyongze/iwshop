<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>个人中心 - {$settings.shopname}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="format-detection" content="telephone=no" />
        <link href="static/css/wshop_uc.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
    </head>
    <body>

        <script data-main="static/script/Wshop/shop_uchome.js?v={$config.cssversion}" src="static/script/require.min.js"></script>

        <div id="wrapper"><div class="close"></div></div>

        {include file="../global/ad/global_top.tpl"}

        <div class="uc-headwrap" style='background-image: url(static/images/ucbag/bag{$bagRand}.jpg);'>
            <div class="uc-head">
                <a class="headwrap"><img src="{$userinfo.client_head}/0" /></a>
                <span class="uc-name">{$userinfo.client_nickname}</span>
                <span class="uc-addr">{$level.level_name}</span>
            </div>
            <div class="comspreadstat clearfix">
                <span class="spread-item"><b>{$userinfo.client_credit}</b>积分</span>
                <span class="spread-item"><b>&yen;{$userinfo.client_money}</b>余额</span>
                <span class="spread-item" data-swclass="uc-section-hover" onclick="location.href = '{$docroot}?/Uc/envslist';"><b>{$count_envs}</b>红包</span>
                <span class="spread-item" data-swclass="uc-section-hover" onclick="location.href = '{$docroot}?/Uc/uc_likes';"><b>{$count_like}</b>收藏</span>
            </div>
        </div>

        <!-- home nav -->
        <div class="uc-section" data-swclass="uc-section-hover" onclick="location.href = '{$docroot}?/Uc/orderlist';"><i class='dingdan'></i><b>查看全部已购宝贝</b>我的订单</div>
        
        <div class='uc-order-sec clearfix'>
            <a class='uc-order-btn fukuan' href="{$docroot}?/Uc/orderlist/status=unpay"><i></i>待付款<b class='prices'>{$count[0]}</b></a>
            <a class='uc-order-btn fahuo' href="{$docroot}?/Uc/orderlist/status=payed"><i></i>待发货<b class='prices'>{$count[1]}</b></a>
            <a class='uc-order-btn shouhuo' href="{$docroot}?/Uc/orderlist/status=delivering"><i></i>待收货<b class='prices'>{$count[2]}</b></a>
            <a class='uc-order-btn pinjia' href="{$docroot}?/Uc/orderlist/status=received"><i></i>待评价<b class='prices'>{$count[3]}</b></a>
            <a class='uc-order-btn tuikuan' href="{$docroot}?/Uc/orderlist/status=canceled"><i></i>退款<b class='prices'>{$count[4]}</b></a>
        </div>

        <div class="uc-section" data-swclass="uc-section-hover" onclick="location.href = '{$docroot}?/Uc/uc_likes/'"><i></i><b>我喜欢，我收藏</b>我的收藏</div>

        <div class="uc-section" data-swclass="uc-section-hover" onclick="location.href = '{$docroot}?/Uc/credit_exchange/'"><i class='credit'></i><b>您有{$userinfo.client_credit}积分可兑换</b>积分兑换</div>

        {if $companyOn}
            {if $userinfo.is_com eq 1}<div class="uc-section" data-swclass="uc-section-hover" onclick="location.href = '{$docroot}?/Uc/companySpread/'"><i class='hezuo'></i><b>总收益：{$income} &yen;</b>我的代理</div>{/if}
            {if $userinfo.is_com eq 1}<div class="uc-section" data-swclass="uc-section-hover" id="companyQrcode"><i class='qrcode'></i><b>一起来推广吧</b>推广分享</div>{/if}
            {if $userinfo.is_com eq 0}<div class="uc-section" data-swclass="uc-section-hover" onclick="location.href = '{$docroot}?/Company/companyRequest/'"><i class='hezuo'></i><b>加入代理，共同成长</b>成为代理</div>{/if}
                {/if}

        {include file="../global/ad/uc_bottom.tpl"}

        {include file="../global/footer.tpl"}

    </body>
</html>