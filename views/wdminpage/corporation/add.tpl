<form id="form_alter_company" style="width:250px;padding:5px 10px;">
    <div class="gs-label">企业名称</div>
    <div class="gs-text">
        <input type="text" id="cname" value="{$ent.ename}" />
    </div>
    <div class="gs-label">联系电话</div>
    <div class="gs-text">
        <input type="text" id="cphone" value="{$ent.ephone}" />
    </div>
</form>
<div class="center" style="margin:0 -15px;">
    <a class="wd-btn primary" style="width:150px" id="al-com-save" data-id="{$ent.id}" href="javascript:;">
        {if $mod eq 'add'}提交{else}保存{/if}
    </a>
</div>