<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>你的朋友{$userInfo.client_name}发起了一个众筹，快来支持{if $userInfo.client_sex eq 'm'}他{/if}{if $userInfo.client_sex eq 'f'}她{/if}{if !$userInfo.client_sex}TA{/if}实现愿望吧！</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="format-detection" content="telephone=no">
        <link href="{$docroot}static/css/wshop_cart.css?v={$cssversion}1" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="{$docroot}static/script/crypto-md5.js"></script>
    </head>
    <body>

        <input type="hidden" id="orderId" value="{$orderInfo.order_id}" />

        <div id="userWra"></div>
        <div id="userHeadwra">
            <img class="userHead" id="uhead" src="{$userInfo.client_head}/0" width="100" height="100"  />
        </div>

        <div id="reqinfo">
            <p>{$userInfo.client_name} 邀请请您一起付</p>

            <div class="pdInfo" style="margin-bottom: 10px;">
                已有<span class="prices">{$reqcount}</span>人参加，已募集<span class="prices" id="reqAmountCurr">&yen;{$reqed}</span>元，目标<span class="prices" id="reqAmountTotal" data-amount="{$orderInfo.order_amount - $reqed}">&yen;{$orderInfo.order_amount}元</span></span>
                <div id="reqProcess">
                    <div id="reqProcessBar" style="width: {(($reqed / $orderInfo.order_amount) * 100)|string_format:"%.2f"}%;"></div>
                </div>
                <p id="reqPercent">{(($reqed / $orderInfo.order_amount) * 100)|string_format:"%.2f"}%</p>
            </div>

            <header class="serialCaption"><span>订单信息</span></header>

            <div class="pdInfo">
                {foreach from=$orderDetail item=pd}
                    <div class="pinfo clearfix">
                        <img width="50" height="50" src="{if $config.usecdn}{$config.imagesPrefix}product_hpic/{$pd.catimg}_x100{else}static/Thumbnail/?w=100&h=100&p={$config.productPicLink}{$pd.catimg}{/if}" />
                        <div class="r">
                            <a href="?/vProduct/view/id={$pd.product_id}">{$pd.product_name}</a>
                            <span class="prices">&yen;{$pd.product_discount_price}</span>
                        </div>
                    </div>
                {/foreach}
            </div>

            {if $reqlist|count > 0}
                <header class="serialCaption"><span>参与朋友</span></header>
                <div class="pdInfo">
                    {foreach from=$reqlist item=li}
                        <div class="pinfo clearfix">
                            <img width="50" height="50" src="{$li.user.client_head}/64" />
                            <div class="r">
                                <i>{$li.dt}</i>
                                <a href="javascript:;">{$li.user.client_name}</a>
                                <span class="prices">&yen;{$li.amount}</span>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/if}

            {if !$isfinish}
                <div class="reqAmount">
                    <a class="reqAmountBtn" data-val="0.01">0.01元</a>
                    <a class="reqAmountBtn" data-val="2">2元</a>
                    <a class="reqAmountBtn" data-val="3">3元</a>
                    <a class="reqAmountBtn" data-val="5">5元</a>
                    <a class="reqAmountBtn" data-val="{$orderInfo.order_amount - $reqed}">包了</a>
                </div>
                <div class="button green" id="wechat-payment-btn">为{if $userInfo.client_sex eq 'm'}他{/if}{if $userInfo.client_sex eq 'f'}她{/if}{if !$userInfo.client_sex}TA{/if}付款</div>
            {/if}
        </div>

        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script type="text/javascript">
            wx.config({
                debug: false,
                appId: '{$signPackage.appId}',
                timestamp: {$signPackage.timestamp},
                nonceStr: '{$signPackage.nonceStr}',
                signature: '{$signPackage.signature}',
                jsApiList: ['chooseWXPay', 'onMenuShareAppMessage', 'onMenuShareTimeline']
            });
            wx.onMenuShareTimeline({
                title: document.title, // 分享标题
                link: location.href, // 分享链接
                imgUrl: "{$userInfo.client_head}/0"
            });
            wx.onMenuShareAppMessage({
                title: document.title, // 分享标题
                link: location.href, // 分享链接
                imgUrl: "{$userInfo.client_head}/0"
            });
        </script>
        <script data-main="{$docroot}static/script/Wshop/shop_reqpay.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script>
        {include file="../global/copyright.tpl"}

    </body>
</html>