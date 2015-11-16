{if $address}
    <div style='min-width:400px;max-height:300px;background: #fff;overflow-y: auto;'>
        <table class='cus-addrlist'>
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
    </div>
{else}
    <div class='fancyEmpty'>
        暂无数据
    </div>
{/if}