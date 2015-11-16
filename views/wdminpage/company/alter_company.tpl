<form id="form_alter_company" style="width:350px;padding:5px 10px;">
    <div class='clearfix'>
        <div style='float:left;width:30%;'>
            <div class="gs-label">姓名</div>
            <div class="gs-text">
                <input type="text" name="name" value="{$com.name}" autofocus/>
            </div>
        </div>
        <div style='float:left;width:46%;margin-left: 2%;'>
            <div class="gs-label">电话</div>
            <div class="gs-text">
                <input type="text" name="phone" value="{$com.phone}" />
            </div>
        </div>
        <div style='float:right;width:20%;'>
            <div class="gs-label">返点比例</div>
            <div class="gs-text">
                <input type="text" name="return_percent" value="{if $com.return_percent}{$com.return_percent}{else}0.05{/if}" />
            </div>
        </div>
    </div>
    <div class='clearfix'>
        <div style='float:left;width:48%;'>
            <div class="gs-label">邮箱</div>
            <div class="gs-text">
                <input type="text" name="email" value="{$com.email}" />
            </div>
        </div>
        <div style='float:right;width:48%;'>
            <div class="gs-label">后台密码</div>
            <div class="gs-text">
                <input type="password" name="password" value="" />
            </div>
        </div>
    </div>
    <div class='clearfix'>
        <div style='float:left;width:48%;'>
            <div class="gs-label">收款银行</div>
            <div class="gs-text">
                <input type="text" name="bank_name" value="{$com.bank_name}" />
            </div>
        </div>
        <div style='float:right;width:48%;'>
            <div class="gs-label">收款姓名</div>
            <div class="gs-text">
                <input type="text" name="bank_personname" value="{$com.bank_personname}" />
            </div>
        </div>
    </div>
    <div class="gs-label">收款账户</div>
    <div class="gs-text">
        <input type="text" name="bank_account" value="{$com.bank_account}" />
    </div>
    <div class="gs-label">身份证号码 <b>可选</b></div>
    <div class="gs-text">
        <input type="text" name="person_id" value="{$com.person_id}" />
    </div>
    {if $mod eq 'add'}
        <input type='hidden' value='1' name='verifed' />
        <input type='hidden' value='{$date}' name='join_date' />
    {/if}
</form>
<div class="center" style="margin:0 -15px;">
    <a class="wd-btn primary" style="width:150px" id="al-com-save" data-id="{$com.id}" href="javascript:;">
        {if $mod eq 'add'}提交{else}保存{/if}
    </a>
</div>