<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>{$title} - {$settings.shopname}</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="format-detection" content="telephone=no" />
        <link href="{$docroot}static/css/wshop_cart.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <link href="{$docroot}static/css/base_animate.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="http://cdns.ycchen.cc/scripts/crypto-md5.js"></script>
    </head>
    <body>

        {include file="../global/top_nav.tpl"}

        {include file="../global/ad/global_top.tpl"}

        <div id="addrPick"></div>
        <input type="hidden" id="promId" value="{$promId}" />
        <input type="hidden" id="promAva" value="{$promAva}" />
        <input type="hidden" id="payOn" value="{if !$config.order_nopayment}1{else}0{/if}" />
        <input type="hidden" id="addrOn" value="{if $config.wechatVerifyed and $config.useWechatAddr}1{else}0{/if}" />
        <input type="hidden" id="paycallorderurl" value="{$docroot}?/Order/ajaxCreateOrder" />

        <header class="Thead">收货信息</header>

        <!-- 收货地址 -->
        <div id="express-bar"></div>
        <div id="express_address" href="javascript:;">
            <div id="wrp-btn">点击选择收货地址</div>
            <div class="express-person-info clearfix">
                <div class="express-person-name">
                    <span id="express-name"></span><span id="express-person-phone"></span>
                </div>
            </div>
            <div class="express-address-info">
                <span id="express-address"></span>
            </div>
        </div>

        <header class="Thead">订单信息</header>

        <div id="orderDetailsWrapper" data-minheight="68px"></div>

        <div id="optinfo" class='hidden'>
            {if $envs|count > 0}
                <header class="Thead">红包折扣</header>
                <div id="userEnvsList">
                    {foreach from=$envs item=env}
                        <div id="uEnv-{$env.envid}" data-id="{$env.id}" class="envsItem" data-pid="{$env.pid}" data-req="{$env.req_amount}" data-dis="{$env.dis_amount}">
                            <span>{$env.name}({$env.pidx})</span>
                            <i></i>
                        </div>
                    {/foreach}
                </div>
            {/if}
            {if $settings.reci_open eq 1}
                <header class="Thead">发票信息</header>
                <div id="userReciInfo">
                    <div class='reciItem'>
                        <span style='border-bottom:none;'>是否开发票</span>
                        <i></i>
                    </div>
                    <div id='reciWrap'>
                        <div class="gs-text">
                            <input type="text" id="reci_head" placeholder="发票抬头" />
                        </div>
                        <div style="font-size: 12px;margin-top:10px;">发票内容：
                            {foreach from=$recis item=rec name=recn}
                                <input id='recis{$smarty.foreach.recn.index}' class='recis' name='reciopt[]' type='radio' value='{$rec}' {if $smarty.foreach.recn.index eq 0}checked{/if} /><label onclick='$("#recis{$smarty.foreach.recn.index}").click();'>{$rec}</label>
                            {/foreach}
                        </div>
                    </div>
                </div>
            {/if}
        </div>

        <section class="orderopt">
            <span class="label">配送时间</span>
            <span class="value">随时可以</span>
            <input type="date" id="exptime" name="date" />
        </section>

        <section class="orderopt inp">
            <span class="label">订单备注</span>
            <input type="text" class="Elipsis" value="" id="input-remark" placeholder="选填，可填写您的特殊要求，如颜色、尺码等" />
        </section>

        <!-- 订单总额 -->
        <div id="orderSummay" class='hidden'>
            <div style="display: none">
                <input type="checkbox" id="cart-balance-check"/> 使用余额 <b id="cart-balance-pay">{$userInfo['balance']}</b>
            </div>
            <div>
                运费 : <b class="prices font13" id="order_yunfei">&yen;0.00</b>
            </div>
            <div id="envsDisTip">
                红包 : <b class="prices font13" id="envs_amount">&yen;0.00</b>
            </div>
            <div id="reciTip">
                税费 : <b class="prices font13" id="reciTip_amount">&yen;0.00</b>
            </div>
            <div>
                总价 : <b class="prices font13" id="order_amount_sig">&yen;0.00</b>
            </div>
            <div>
                总计 : <b class="prices" id="order_amount">&yen;0.00</b>
            </div>
        </div>

        <!-- 微信支付按钮 -->
        <div class="button green" style='display: none' id="wechat-payment-btn">{if $config.order_nopayment}马上下单{else}微信安全支付{/if}</div>
        <!-- 找人代付按钮 -->
        {*        {if $promId eq ''}<div class="button green" style='display: none' id="wechat-reqpay-btn">{if $config.order_nopayment}马上下单{else}找朋友帮我付{/if}</div>{/if}*}
        <!-- 微信JSSDK -->
        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

        <script type="text/javascript">
            wx.config({
                debug: false,
                appId: '{$signPackage.appId}',
                timestamp: {$signPackage.timestamp},
                nonceStr: '{$signPackage.nonceStr}',
                signature: '{$signPackage.signature}',
                jsApiList: ['chooseWXPay']
            });
            addrsignPackage = {$addrsignPackage};
        </script>

        <script data-main="{$docroot}static/script/Wshop/shop_cart.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script>

        {include file="../global/copyright.tpl"}

    </body>
</html>
