{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/exports/exports_transform.js</i>
<input type="hidden" value="{$islocal}" id="islocal" />
<input type="hidden" value="{$ids}" id="ids" />
<div class="search-left-box{if $islocal neq 1} hidden{/if}">
    <select style='width: 130px;margin-top: 8px;' id='exp-type-all'>
        {if $islocal eq 1}
            <option value='2'>微店 -> 保税仓</option>
            <option value='3'>微店 -> 广州仓</option>
        {else}
            <option value='0'>苏宁 -> 保税仓</option>
            <option value='1'>苏宁 -> 广州仓</option>
        {/if}
    </select>
</div>
<div class='gmess-sending'></div>
<div style="margin-bottom: 42px;" id="od-exp-frame">

</div>
<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary hidden" id="data-exp-trans" href="javascript:;">开始转换</a>  
    <a class="wd-btn default" onclick="history.go(-1);" href="javascript:;">返回</a>
    <a id="_trig1" href="#uploadDiv"></a>
    <a id="_trig2" href="#downloadDiv"></a>
</div>
<div style="display: none;height: 150px;width: 150px;" id="uploadDiv">
    <a class="wd-btn primary" id="data-exp-upload" style="width: 140px;margin-top: 60px">选择要转换的文件</a>
</div>
<div style="display: none;text-align: center;" id="downloadDiv">
    <img src="{$docroot}static/images/admin/iconfont-roundcheck.png" style="display: block;margin:0 auto;opacity: 0.9" height="128px"/>
</div>
{include file='../__footer.tpl'} 