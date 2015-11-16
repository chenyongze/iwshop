{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/customers/customer_level.js</i>
<div id="DataTables_Table_0_filter" class="dataTables_filter clearfix">
    <div class="button-set">
        <a class="button gray" href="javascript:;" onclick='location.reload()'>刷新</a>
        <a class="button fancybox.ajax" data-fancybox-type="ajax" id='add-level' href="{$docroot}?/WdminPage/ajaxmodlevel/mod=add">添加等级</a>
    </div>
</div>
<table cellpadding=0 cellspacing=0 class="dTable">
    <thead>
        <tr>
            <th>名称</th>
            <th>积分要求</th>
            <th>折扣</th>
            <th>积分返比</th>
            <th style="width: 80px;">操作</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$levels item=lev}
            <tr>
                <td>{$lev.level_name}</td>
                <td>{$lev.level_credit}</td>
                <td>{$lev.level_discount}%</td>
                <td>{$lev.level_credit_feed}%</td>
                <td>
                    <a class="fancybox.ajax levedit" data-fancybox-type="ajax" href="{$docroot}?/WdminPage/ajaxmodlevel/mod=edit&id={$lev.id}">编辑</a>
                    {if $lev.id > 0}<a class="del delevel" href="javascript:;" data-id="{$lev.id}">删除</a>{/if}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
{include file='../__footer.tpl'} 