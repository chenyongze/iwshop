{include file='../__header.tpl'}
<link href="/static/css/base_pagination.css" type="text/css" rel="Stylesheet" />
<i id="scriptTag">{$docroot}static/script/Wdmin/customers/iframe_list_customer.js</i>
<input type='hidden' id='gid' value='{$gid}' />
<div id="DataTables_Table_0_filter" class="dataTables_filter clearfix">
    <div class="search-w-box"><input type="text" class="searchbox" placeholder="输入搜索内容" /></div>
    <div class="button-set">
        <a class="button primary" href="{$docroot}?/WdminPage/iframe_alter_customer/id=0">添加会员</a>
    </div>
</div>

<table cellpadding=0 cellspacing=0 class="dTable bottom40">
    <thead>
        <tr>
            <th class='hidden'> </th>
            <th>头像</th>
            <th>姓名</th>
            <th>性别</th>
            <th>省市</th>
            <th>订单数量</th>
            <th>代理</th>
            <th>等级</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<!-- 会员table模版开始，可以使用script（type设置为text/html）来存放模板片段，并且用id标示 -->
<script id="t:ct_list" type="text/html">
    {literal}
        <%for(var i=0;i<list.length;i++){%>
            <tr>
                <td class="hidden"><%=list[i].cid%></td>
                <td><img class="ccl-head" alt="<%=list[i].client_name%>" src="<%if(!list[i].client_head){%><%=shoproot%>static/images/login/profle_1.png<%}else{%><%=list[i].client_head%>/64<%}%>"/>
                </td>
                <td><%=list[i].client_name%></td>
                <td><%=list[i].client_sex%></td>
                <td><%=list[i].client_province%><%=list[i].client_city%></td>
                <td>
                    <%if(list[i].order_count == 0){%>
                        <%=list[i].order_count%>
                    <%}else{%>
                        <a href="?/WdminPage/customer_profile/id=<%=list[i].cid%>"><%=list[i].order_count%></a>
                    <%}%>
                </td>
                <td>
                    <%=list[i].company_name ? list[i].company_name : ""%>
                </td>
                <td><%=list[i].levelname%></td>
                <td>
                    <a class="us-edit" href="?/WdminPage/iframe_alter_customer/id=<%=list[i].cid%>">编辑</a>
                    <a class="us-view" href="?/WdminPage/customer_profile/id=<%=list[i].cid%>">查看</a>
                </td>
            </tr>
            <%}%>
        {/literal}
    </script>
    <!-- 模板结束 -->

    <div class="fix_bottom textRight fixed">
        <div id="pager-bottom"><ul class="pagination-sm"></ul></div>
    </div>

    {include file='../__footer.tpl'} 