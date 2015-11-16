{strip}{if $olistcount == 0}0{else}
        <table class="dTable">
            <thead>
                <tr>
                    <th class="hidden"></th>
                    <th class="od-exp-check"><input class="checkAll" type="checkbox" /></th>
                    <th>订单编号</th>
                    <th>代理</th>
                    <th>收货人</th>
                    <th>收货电话</th>
                    <th>订单金额</th>
                    <th>运费</th>
                    <th>商品信息</th>
                    <th>商品数量</th>
                    <th>下单时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                {section name=oi loop=$orderlist}
                    <tr id='order-exp-{$orderlist[oi].order_id}'>
                        <td class="hidden">{$orderlist[oi].order_id}</td>
                        <td class="od-exp-check"><input class='pd-exp-checks' type="checkbox" data-id='{$orderlist[oi].order_id}' /></td>
                        <td>{$orderlist[oi].serial_number}</td>
                        <td data-comid='{$orderlist[oi].company.id}'>{$orderlist[oi].company.name}</td>
                        <td>{$orderlist[oi].address.user_name}</td>
                        <td>{$orderlist[oi].address.tel_number}</td>
                        <td class="prices font12">&yen;{$orderlist[oi].order_amount}</td>
                        <td class="prices font12">&yen;{$orderlist[oi].order_yunfei}</td>
                        <td>
                            <a class="various fancybox.ajax" 
                               data-orderid="{$orderlist[oi].order_id}"
                               data-fancybox-type="ajax" 
                               href="{$docroot}?/WdminAjax/loadOrderDetail/id={$orderlist[oi].order_id}">点击查看</a>
                        </td>
                        <td>{$orderlist[oi].product_count} 件</td>
                        <td>{$orderlist[oi].order_time}</td>
                        <th class="gray font12">
                            <a data-orderid="{$orderlist[oi].order_id}" class="various fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/WdminAjax/loadOrderDetail/id={$orderlist[oi].order_id}">马上发货</a>
                        </th>
                    </tr>
                {/section}
            </tbody>
        </table>
{/if}{/strip}