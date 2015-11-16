{include file='../__header.tpl'}
<i id="scriptTag">page_list_customers</i>
<div class="clearfix">
    <div id="categroys" style="padding:0;width:170px;">
        <div style="margin-top:45px;">
            {foreach from=$group item=g name=groupn}
                <div data-id="{$g.id}" class="list-user-group-item Elipsis{if $smarty.foreach.groupn.first} selected{/if}">
                    {$g.level_name}<em>({$g.count})</em>
                    {if $g.id > 3}<a class="del fancybox.ajax" data-fancy-type="ajax" href="?/FancyPage/fancyAlterGroup/id={$g.id}&name={$g.name}"> </a>{/if}
                </div>
            {/foreach}
        </div>
        <div class="center">
            <a id="addGroup" data-fancy-type="ajax" class="wd-btn primary  fancybox.ajax" href="?/FancyPage/fancyAddGroup/" style="margin-top:10px;">新增分组</a>
        </div>
    </div>
    <div id="cate_settings" style="margin-left:170px;">
        <div id="iframe_loading" style="top:0;"></div>
        <iframe id="iframe_customer" src="" style="display: block;" width="100%" frameborder="0"></iframe>
    </div>
</div>
{include file='../__footer.tpl'} 