{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/orders/orders_history_address.js</i>
<table class='dTable'>
    <thead>
        <tr>
            <th>姓名</th>
            <th>电话</th>
            <th>邮编</th>
            <th style='padding-right: 10px;'>地址</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$address item=addr}
            <tr>
                <td>{$addr.user_name}</td>
                <td>{$addr.tel_number}</td>
                <td>{$addr.postal_code}</td>
                <td style='padding-right: 10px;'>{$addr.address}</td>
            </tr>
        {/foreach}
    </tbody>
</table>
{include file='../__footer.tpl'} 