<table cellpadding=0 cellspacing=0 class="dTable">
    <thead>
        <tr>
            <th class='hidden'></th>
            <th>订单编号</th>
            <th>代理</th>
            <th>收货人</th>
            <th>收货电话</th>
            <th>商品信息</th>
            <th>订单金额</th>
            <th>运费</th>
            <th>商品数量</th>
            <th>快递信息</th>
            <th>状态</th>
            <th>下单时间</th>
        </tr>
    </thead>
    <tbody>
        {strip}
            {section name=oi loop=$orderlist}
                <tr {if $smarty.section.oi.index is odd by 1}class="odd"{/if}>
                    <td class='hidden'>{$orderlist[oi].order_id}</td>
                    <td>{$orderlist[oi].serial_number}</td>
                    <td data-comid='{$orderlist[oi].company.id}'>{$orderlist[oi].company.name}</td>
                    <td>{$orderlist[oi].address.user_name}</td>
                    <td>{$orderlist[oi].address.tel_number}</td>
                    <td>
                        <a class="od-list-pdinfo fancybox.ajax" 
                           data-fancybox-type="ajax" 
                           href="{$docroot}?/WdminAjax/loadOrderDetail/id={$orderlist[oi].order_id}">点击查看</a>
                    </td>
                    <td class="xleft prices font12">&yen;{$orderlist[oi].order_amount}</td>
                    <td class="prices font12">&yen;{$orderlist[oi].order_yunfei}</td>
                    <td>{$orderlist[oi].product_count} 件</td>
                    <td>
                        {if $orderlist[oi].express_code ne ''}
                            {if $orderlist[oi].status == 'received' || $orderlist[oi].status == 'delivering'}
                                <a class="pd-list-viewExp fancybox.ajax" id="od-exp-view{$orderlist[oi].order_id}"
                                   data-fancybox-type="ajax" 
                                   href="{$docroot}?/Wdmin/ajaxLoadOrderExpress/com={$orderlist[oi].express_com}&nu={$orderlist[oi].express_code}">{$orderlist[oi].expressName}</a>
                            {/if}
                        {else}
                            无
                        {/if}
                    </td>
                    <td class="orderstatus {$orderlist[oi].status}">{$orderlist[oi].statusX}</td>
                    <td>{$orderlist[oi].order_time}</td>
                </tr>
            {/section}
        {/strip}
    </tbody>
</table>