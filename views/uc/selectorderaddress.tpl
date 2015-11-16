{if !$bodyonly}
    <!DOCTYPE HTML>
    <html>
        <head>
            <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
            <title>收货地址管理 - {$settings.shopname}</title>
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
            <meta name="apple-mobile-web-app-capable" content="yes" />
            <meta name="apple-mobile-web-app-status-bar-style" content="black" />
            <meta name="format-detection" content="telephone=no" />
            <link href="static/css/wshop_main_style.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
            <link href="static/css/wshop_uc_style.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
            <link href="static/css/common.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
            <script type="text/javascript" src="static/script/jquery-2.1.1.min.js?v={$cssversion}"></script>
            <script type="text/javascript" src="static/script/main.js?v={$cssversion}"></script>
        </head>
        <body>{/if}
            {*            <div class="addr-manage">
            <a href="#">管理</a>
            </div>*}
            <div id="addr-add">
                <input type="text" placeholder="收货人姓名" id="dt-name"/>
                <input type="text" placeholder="收货人电话" id="dt-tel"/>
                <textarea id="dt-address" placeholder="详细地址"></textarea>
                <a class="button green" id="addr-add-btn" style="margin: 12px;">确认添加</a>
                <a class="button green" id="addr-add-btn-back" style="margin: 12px;">返回</a>
            </div>
            <div id="addr-select">
                {foreach from=$addrs item=addr}
                    <div class="addrw" data-id="{$addr.addr_id}" data-name="{$addr.name}" data-tel="{$addr.tel}" data-address="{$addr.address}">
                        <div class="cot clearfix">
                            <span class="l">{$addr.name}</span>
                            <span class="r">{$addr.tel}</span>
                        </div>
                        <div class="add">{$addr.address}</div>
                    </div>
                {/foreach}
                <div class="center" style="margin:0 auto;width: 160px;">
                    <a class="button green" id="addr-add-btn1">添加收货地址</a>
                </div>
            </div>
            {if !$bodyonly}
                <script type="text/javascript">
                    WeixinJSBridgeReady(edtAddOnload);
                </script>
            </body>
        </html>
    {/if}