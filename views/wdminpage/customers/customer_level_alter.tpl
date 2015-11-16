<form id="form_alter_company" style="width:350px;padding:5px 10px;">

    <div class="gs-label">名称</div>
    <div class="gs-text">
        <input type="text" id="name" value="{$com.level_name}" />
    </div>    

    <div class="gs-label">积分要求</div>
    <div class="gs-text">
        <input type="text" id="credit" value="{$com.level_credit}" />
    </div>

    <div class="gs-label">享受折扣 <b>1-100的百分数</b></div>
    <div class="gs-text">
        <input type="text" id="discount" value="{$com.level_discount}" />
    </div>

    <div class="gs-label">积分返比 <b>1-100的百分数</b></div>
    <div class="gs-text">
        <input type="text" id="feed" value="{$com.level_credit_feed}" />
    </div>

    <div style="text-align: right;">可升级<input type="checkbox" id="upable" {if $com.upable eq 1}checked{/if}/></div>

    <input type="hidden" id="lid" value="{if $com.id > 0}{$com.id}{else}-1{/if}" />
</form>
<div class="center" style="margin:0 -15px;">
    <a class="wd-btn primary" style="width:150px" id="al-com-save" data-id="{$com.id}" href="javascript:;">
        {if $com.id > 0}提交{else}保存{/if}
    </a>
</div>