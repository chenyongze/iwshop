{strip}{if $olistcount == 0}0{else}
        <table class="dTable">
            <thead>
                <tr>
                    <th>订单编号</th>
                    <th>代理</th>
                    <th>收货人</th>
                    <th>收货电话</th>
                    <th>订单金额</th>
                    <th>商品信息</th>
                    <th>商品数量</th>
                    <th>下单时间</th>
                    <th>订单状态</th>
                </tr>
            </thead>
            <tbody>
                {section name=oi loop=$orderlist}
                    <tr id='order-exp-{$orderlist[oi].order_id}'>
                        <td>{$orderlist[oi].serial_number}</td>
                        <td data-comid='{$orderlist[oi].company.id}'>{$orderlist[oi].company.name}</td>
                        <td>{$orderlist[oi].address.user_name}</td>
                        <td>{$orderlist[oi].address.tel_number}</td>
                        <td class="prices font12">&yen;{$orderlist[oi].order_amount}</td>
                        <td>
                            <a class="various fancybox.ajax" 
                               data-orderid="{$orderlist[oi].order_id}"
                               data-fancybox-type="ajax" 
                               href="{$docroot}?/WdminAjax/loadOrderDetail/id={$orderlist[oi].order_id}">点击查看</a>
                        </td>
                        <td>{$orderlist[oi].product_count} 件</td>
                        <td>{$orderlist[oi].order_time}</td>
                        <td class="orderstatus {$orderlist[oi].status}">{$orderlist[oi].statusX}</td>
                    </tr>
                {/section}
            </tbody>
        </table>
{/if}{/strip}