<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>我的推广 - {$settings.shopname}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="format-detection" content="telephone=no">
        <link href="static/css/wshop_company_center.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="static/script/jquery-2.1.1.min.js?v={$cssversion}"></script>
        <script type="text/javascript" src="static/script/main.js?v={$cssversion}"></script>
    </head>
    <body>
        <input type="hidden" value="{$status}" id="status" />

        {include file="../global/top_nav.tpl"}

        <div class="comspreadstat clearfix">
            <span class="spread-item">今日<b>&yen; {if $stat_data['incometot']}{$stat_data['incometod']}{else}0{/if}</b></span>
            <span class="spread-item">昨日<b>&yen; {if $stat_data['incometotyet']}{$stat_data['incometotyet']}{else}0{/if}</b></span>
            <span class="spread-item">总计<b>&yen; {if $stat_data['incometot']}{$stat_data['incometot']}{else}0{/if}</b></span>
            <span class="spread-item">名下<b>{if $stat_data['ucount']}{$stat_data['ucount']}{else}0{/if}</b></span>
        </div>

        <header class="Thead">名下会员</header>

        <div id="ulist">
            {foreach from=$stat_data.ulist item=u}
                <section class="ulist clearfix">
                    <img src="{$u.client_head}/64" />
                    <div class="info">
                        <p>{$u.client_nickname}</p>
                        <p>订单：<b>{$u.od}</b> 收益：<b>{$u.oamount}&yen;</b></p>
                    </div>
                </section>
            {/foreach}
        </div>

        {include file="../global/footer.tpl"}

    </body>
</html>