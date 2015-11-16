{strip}{if $olistcount == 0}0{else}
        <table class="dTable">
            <thead>
                <tr>
                    <th class="hidden"></th>
                    <th style="width:50px;padding-left: 0;"><input class="checkAll" type="checkbox" /></th>
                    <th>订单编号</th>
                    <th>收货人</th>
                    <th>收货电话</th>
                    <th>订单金额</th>
                    <th>运费</th>
                    <th>商品数量</th>
                    <th>下单时间</th>
                </tr>
            </thead>
            <tbody>
                {section name=oi loop=$orderlist}
                    <tr id='order-exp-{$orderlist[oi].order_id}'>
                        <td class="hidden">{$orderlist[oi].order_id}</td>
                        <td style="width:50px;padding-left: 0;"><input class='pd-exp-checks' type="checkbox" data-spid='{$s.id}' data-id='{$dets.id}' data-name='{$dets.det_name}' /></td>
                        <td>{$orderlist[oi].serial_number}</td>
                        <td>{$orderlist[oi].address.user_name}</td>
                        <td>{$orderlist[oi].address.tel_number}</td>
                        <td class="prices font12">&yen;{$orderlist[oi].order_amount}</td>
                        <td class="prices font12">&yen;{$orderlist[oi].order_yunfei}</td>
                        <td>{$orderlist[oi].product_count} 件</td>
                        <td>{$orderlist[oi].order_time}</td>
                    </tr>
                {/section}
            </tbody>
        </table>
{/if}{/strip}