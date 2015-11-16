{section name=oi loop=$orders}
    <div class="uc-orderitem" id="orderitem{$orders[oi].order_id}">
        <div class="uc-seral clearfix">
            <p class="order_serial">订单号：{$orders[oi].serial_number}</p>
            <p class="order_status">{$orders[oi].statusX}</p>
        </div>
        {section name=di loop=$orders[oi]['data']}
            <div class="clearfix items" onclick="location = '{$docroot}?/Order/expressDetail/order_id={$orders[oi].order_id}';">
                <img class="ucoi-pic" src="{$docroot}static/Thumbnail/?w=100&h=100&p=/uploads/product_hpic/{$orders[oi]['data'][di].catimg}">
                <div class="ucoi-con">
                    <!-- 商品标题 -->
                    <span class="title" style='height:42px;'>{$orders[oi]['data'][di].product_name}</span>
                    <!-- 商品单价 -->
                    <span class="price"><span class="dprice">&yen;{$orders[oi]['data'][di].product_discount_price}</span> x <span class="dcount">{$orders[oi]['data'][di].product_count}</span></span>
                </div>
            </div>
        {/section}
        <div class="uc-summary clearfix" style='padding:8px 7px;text-align:right;'>
            <div class="sum">
                实付款: <span class="dprice">&yen;{$orders[oi].order_amount}</span>
            </div>
            {if $orders[oi].status == "unpay"}
                <a class="olbtn cancel" href="javascript:;" onclick="Orders.cancelOrder({$orders[oi].order_id}, this);">取消订单</a>
                {*                <a class="olbtn wepay" href="javascript:confirmExpress({$orders[oi].order_id});">微信支付</a>*}
            {else if $orders[oi].status == "payed"}
                <a class="olbtn cancel" href="javascript:;" onclick="Orders.cancelOrder({$orders[oi].order_id}, this);">取消订单</a>
            {else if $orders[oi].status == "delivering"}
                <a class="olbtn comfirm" href="javascript:Orders.confirmExpress({$orders[oi].order_id});">确认收货</a>
                <a class="olbtn express" href="?/Order/expressDetail/order_id={$orders[oi].order_id}">查看物流</a>
            {else if $orders[oi].status == "received" and $orders[oi].is_commented eq 0}
                <a class="olbtn express" href="?/Order/commentOrder/order_id={$orders[oi].order_id}">订单评价</a>
            {/if}
            <a class="olbtn wepay" href="?/Order/expressDetail/order_id={$orders[oi].order_id}">订单详情</a>
            {if $orders[oi].isreq}
                <a class="olbtn wepay" href="?/Order/reqPay/id={$orders[oi].order_id}">邀请页面</a>
            {/if}
        </div>
    </div>
{/section}