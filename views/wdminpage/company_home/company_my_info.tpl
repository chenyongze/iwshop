{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/company/company_my_qrcode.js</i>
<div style='width: 430px;margin:0 auto;text-align: center' id='abscenter'>
    <img src="{$qrcode}" width="430px" style='border:1px solid #dedede;' />
    <div class="center" style="margin-top:20px;">
        <a class="wd-btn primary fancybox.ajax" id='alter_info' style="width:150px;" data-fancybox-type="ajax" href="{$docroot}?/WdminPage/alter_company/id={$comid}&mod=edit">修改收款资料</a>
        <a class="wd-btn primary" href="{$qrcode}" target="_blank">下载</a>
    </div>
</div>
{include file='../__footer.tpl'} 