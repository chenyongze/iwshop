{include file='../__header.tpl'}

<i id="scriptTag">{$docroot}static/script/Wdmin/settings/auth_list.js</i>
<div id="list" style="margin-bottom: 40px;">
    <table class="dTable">
        <thead>
            <tr>
                <th>管理员账号</th>
                <th>权限</th>
                <th>最后登录</th>
                <th>最后登录IP</th>
                <th class="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$auths item=auth}
                <tr>
                    <td>{$auth.admin_account}</td>
                    <td class="admin-auth-list" style="display: none;">{$auth.admin_auth}</td>
                    <td>{$auth.admin_last_login}</td>
                    <td>{$auth.admin_ip_address}</td>
                    <td class="center">
                        <a class="lsBtn add-level fancybox.ajax" data-fancybox-type="ajax" href="?/WdminPage/auth_edit/id={$auth.id}">编辑</a>
                        <a class="lsBtn log-level pd-altbtn tip" href="javascript:;">日志</a>
                        <a class="lsBtn del envs_del" data-id="{$auth.id}" href="javascript:;">删除</a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>
<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" id='add-level' style="width:150px" href="{$docroot}?/WdminPage/auth_edit/mod=add">添加账号</a>
</div>

{include file='../__footer.tpl'} 