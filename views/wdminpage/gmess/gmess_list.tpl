{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/gmess/gmess_list.js</i>
<table class='dTable' style="margin-bottom: 40px;">
    <thead>
        <tr>
            <th>编号</th>
            <th>封面</th>
            <th>标题</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$list item=g}
            <tr>
                <td style="color:#888">{$g.id}</td>
                <td>
                    <img class='pdlist-image' src='{$docroot}static/Thumbnail/?w=50&h=50&p={$docroot}uploads/gmess/{$g.catimg}' />
                </td>
                <td>{$g.title}</td>
                <td>{$g.createtime}</td>
                <td>
                    <a href='{$docroot}?/WdminPage/gmess_edit/id={$g.id}'>编辑</a> <a class="gmessView" href="javascript:;" data-href='{$docroot}?/Gmess/view/id={$g.id}'>查看</a> <a class='del del_gmess_btn' data-id='{$g.id}' href='javascript:;'>删除</a>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
<div class="fix_bottom fixed">
    <a class="wd-btn primary" style="width:150px" href="{$docroot}?/WdminPage/gmess_edit/">添加素材</a>
</div>
{include file='../__footer.tpl'} 