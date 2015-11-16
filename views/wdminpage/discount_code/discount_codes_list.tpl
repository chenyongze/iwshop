{include file='../__header.tpl'}
<input type="hidden" value="{$id}" id="qid" />
<i id="scriptTag">{$docroot}static/script/Wdmin/discount_code/codes_list.js</i>
<div id="list" style="margin-bottom: 40px;">
    <table class="dTable">
        <thead>
            <tr>
                <th>编号</th>
                <th>优惠码</th>
                <th>已领取</th>
                <th>领取人</th>
                <th>领取时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$list item=l}
                <tr>
                    <td>{$l.id}</td>
                    <td id="codes-{$l.id}">{$l.codes}</td>
                    <td>{$l.isvalid}</td>
                    <td>{$l.client_name}</td>
                    <td>{$l.rectime}</td>
                    <td>
                        <a class="lsBtn alter-codes fancybox.ajax" data-fancybox-type='ajax' data-id="{$l.id}" href="?/WdminPage/discount_code_alter/id={$l.id}">编辑</a>
                        <a class="lsBtn delete-codes del" data-id="{$l.id}" href="javascript:;">删除</a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>
<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary" id="code_upload" href="javascript:;">批量上传</a>
    <a class="wd-btn primary fancybox.ajax" data-fancybox-type='ajax' id="add_codes" href="?/WdminPage/discount_code_alter/">添加优惠码</a>
    <a class="wd-btn primary" href="javascript:;" onclick="history.go(-1);">返回</a>
</div>

{include file='../__footer.tpl'} 