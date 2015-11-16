{include file='../__header.tpl'}
<input type="hidden" value="{$id}" id="qid" />
<i id="scriptTag">{$docroot}static/script/Wdmin/discount_code/code_list.js</i>
<div id="list" style="margin-bottom: 40px;">
    <table class="dTable">
        <thead>
            <tr>
                <th>编号</th>
                <th>关键字</th>
                <th>优惠价格</th>
                <th>优惠码总量</th>
                <th>优惠码余量</th>
                <th>剩余比例</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$list item=l}
                <tr>
                    <td>{$l.id}</td>
                    <td id="keywords-{$l.id}">{$l.keywords}</td>
                    <td>&yen;{$l.code_discount|string_format:"%.2f"}</td>
                    <td>{$l.code_total}个</td>
                    <td>{$l.code_remains}个</td>
                    <td>{(($l.code_remains / $l.code_total) * 100)|string_format:"%.2f"}%</td>
                    <td>
                        <a class="lsBtn" href="?/WdminPage/discount_codes_list/id={$l.id}">列表</a>
                        <a class="lsBtn alter-discount fancybox.ajax" data-fancybox-type='ajax' data-id="{$l.id}" href="?/WdminPage/discount_alter/id={$l.id}">编辑</a>
                        <a class="lsBtn del delete-discount" data-id="{$l.id}" href="javascript:;">删除</a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>
<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary fancybox.ajax" data-fancybox-type='ajax' id="add_discounts" href="?/WdminPage/discount_alter/">添加活动</a>
</div>

{include file='../__footer.tpl'} 