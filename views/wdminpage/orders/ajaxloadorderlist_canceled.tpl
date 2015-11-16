{strip}{if $olistcount == 0}0{else}
        <table class="dTable">
            <thead>
                <tr>
                    <th>订单编号</th>
                    <th>收货人</th>
                    <th>收货电话</th>
                    <th>订单金额</th>
                    <th>可退款金额</th>
                    <th>运费</th>
                    <th>已发货</th>
                    <th>快递信息</th>
                    <th>商品数量</th>
                    <th>下单时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                {section name=oi loop=$orderlist}
                    <tr id='order-exp-{$orderlist[oi].order_id}'>
                        <td>{$orderlist[oi].serial_number}</td>
                        <td>{$orderlist[oi].address.user_name}</td>
                        <td>{$orderlist[oi].address.tel_number}</td>
                        <td class="prices font12">&yen;{$orderlist[oi].order_amount}</td>
                        <td class="prices font12">&yen;{$orderlist[oi].refundable}</td>
                        <td class="prices font12">&yen;{$orderlist[oi].order_yunfei}</td>
                        <td>{if $orderlist[oi].express_code ne ''}Y{else}N{/if}</td>
                        <td>
                            {if $orderlist[oi].express_code ne ''}
                                <a class="pd-list-viewExp fancybox.ajax" id="od-exp-view{$orderlist[oi].order_id}"
                                   data-fancybox-type="ajax" 
                                   href="{$docroot}?/Wdmin/ajaxLoadOrderExpress/com={$orderlist[oi].express_com}&nu={$orderlist[oi].express_code}">
                            {if $orderlist[oi].expressName eq ''}{$orderlist[oi].express_com}{else}{$orderlist[oi].expressName}{/if}</a>
                        {else}
                        无
                    {/if}
                </td>
                <td>{$orderlist[oi].product_count}件</td>
                <td>{$orderlist[oi].order_time}</td>
                <td class="gray font12">
                    <a data-orderid="{$orderlist[oi].order_id}" class="orderRefund fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/orderRefund/id={$orderlist[oi].order_id}">退款</a>
                </td>
            </tr>
        {/section}
    </tbody>
</table>
{/if}{/strip}