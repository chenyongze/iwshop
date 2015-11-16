{include file='../__header.tpl'}
<i id="scriptTag">page_company_requests</i>
<table cellpadding=0 cellspacing=0 class="dTable" style="margin-bottom: 40px;">
    <thead>
        <tr>
            <th>编号</th>
            <th>姓名</th>
            <th>电话</th>
            <th>邮箱</th>
            <th>身份证号码</th>
            <th>申请时间</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        {strip}
            {foreach from=$companys item=cs}
                <tr>
                    <td>#{$cs.id}</td>
                    <td>{$cs.name}</td>
                    <td>{$cs.phone}</td>
                    <td>{$cs.email}</td>
                    <td>{$cs.person_id}</td>
                    <td>{$cs.join_date}</td>
                    <td>
                        <a class="company-req-p" href="javascript:;" data-id="{$cs.id}">通过</a> / <a class="del company-req-np" href="javascript:;" data-id="{$cs.id}">不通过</a>
                    </td>
                </tr>
            {/foreach}
        {/strip}
    </tbody>
</table>
<div class="fix_bottom fixed">
    <a class="wd-btn primary" id='add_company' style="width:150px" href="#">添加代理</a>
</div>
{include file='../__footer.tpl'} 