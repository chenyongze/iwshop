{include file='../__header.tpl'}
<i id="scriptTag">page_list_companys</i>
<table cellpadding=0 cellspacing=0 class="dTable">
    <thead>
        <tr>
            <th class="hidden">sort</th>
            <th>姓名</th>
            <th>级别</th>
            <th>电话</th>
            <th>邮箱</th>
            <th>加入时间</th>
            <th>返点比例</th>
            <th>名下会员</th>
            <th>成交订单</th>
            <th>未结算金额</th>
            <th>本月收益</th>
            <th>总收益</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        {strip}
            {foreach from=$companys item=cs}
                <tr>
                    <td class="hidden">{$cs.orderscount}</td>
                    <td>{$cs.name}</td>
                    <td>{$cs.level}</td>
                    <td>{$cs.phone}</td>
                    <td>{$cs.email}</td>
                    <td>{$cs.join_date|date_format:"%Y-%m-%d"}</td>                   
                    <td>{$cs.return_percent * 100}%</td>
                    <td><a href="{$docroot}?/WdminPage/list_company_users/id={$cs.id}">{$cs.fellow_count}人</a></td>
                    <td><a href="{$docroot}?/WdminPage/list_company_income/id={$cs.id}">{$cs.orderscount}笔</a></td>
                    <td class="font12 prices">&yen;{$cs.income_unset}</td>
                    <td class="font12 prices">&yen;{$cs.income_month}</td>
                    <td class="font12 prices">&yen;{$cs.income_total}</td>
                    <td>
                        <a class="alter-company fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/WdminPage/alter_company/id={$cs.id}&mod=edit">编辑</a>
                        &nbsp;/ <a href="{$docroot}?/WdminPage/list_company_income/id={$cs.id}">收益</a>
                        &nbsp;/ <a class="com-ajaxqrcode fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/Company/ajaxGetCompanyQrcode/id={$cs.id}">二维码</a>
                        &nbsp;/ <a href="javascript:;" class='com-delete-btn del' data-id='{$cs.id}'>删除</a>
                    </td>
                </tr>
            {/foreach}
        {/strip}
    </tbody>
</table>
<div class="fix_bottom fixed">
    <a class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" id='add_company' style="width:150px" href="{$docroot}?/WdminPage/alter_company/mod=add">添加代理</a>
</div>
{include file='../__footer.tpl'} 