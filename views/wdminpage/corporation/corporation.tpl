{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/corporation/corporation.js</i>
<input type="hidden" id="cat" value="{$cat}" />
<div{if !$iscom} style="margin-bottom: 40px;"{/if}>
    <table class="dTable">
        <thead>
            <tr>
                <th style='width:300px'>企业名称</th>
                <th>员工数量</th>
                <th>优惠码</th>
                <th>订单</th>
                <th>成交额</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=ls from=$comp}
                <tr class="defTr font12">
                    <td>{$ls.ename}</td>
                    <td>{$ls.scount}</td>
                    <td>{$ls.scode}</td>
                    <td>{$ls.sodcount}</td>
                    <td>{if $ls.samount > 0}{$ls.samount}{else}0{/if}&yen;</td>
                    <td>
                        <a class='ent-edit fancybox.ajax' data-fancybox-type='ajax' href='{$docroot}?/WdminPage/addEnterprise/mod=edit&id={$ls.id}'>编辑</a>
                        <a class="com-ajaxqrcode fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/Company/ajaxGetCompanyQrcode/id={$ls.id}">二维码</a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>
<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" id='add_enterprise' style="width:150px" href="{$docroot}?/WdminPage/addEnterprise/mod=add">添加企业</a>
</div>
{include file='../__footer.tpl'} 