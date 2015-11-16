{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/settings/base.js</i>

<form style="padding:15px 20px;padding-bottom: 70px;" id="settingFrom">

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>功能开启</span>
        </div>
        <div class="fv2Right">
            <select name='reci_open'>
                <option value='1' {if $settings.reci_open eq 1}selected{/if}>开启</option>
                <option value='0' {if $settings.reci_open eq 0}selected{/if}>关闭</option>
            </select>
            <div class='fv2Tip'>是否开启发票功能</div>
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>发票类型</span>
        </div>
        <div class="fv2Right">
            <input type="text" class="gs-input" name="reci_cont" value="{$settings.reci_cont}" autofocus/>
            <div class='fv2Tip'>设置发票类型，以半角逗号(,)隔开</div>
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>税点设置</span>
        </div>
        <div class="fv2Right">
            <input type="text" class="gs-input" name="reci_perc" value="{$settings.reci_perc}" autofocus/>
            <div class='fv2Tip'>设置发票税点，范围：1-100百分数</div>
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>包括运费</span>
        </div>
        <div class="fv2Right">
            <select name='reci_exp_open'>
                <option value='1' {if $settings.reci_exp_open eq 1}selected{/if}>包括运费</option>
                <option value='0' {if $settings.reci_exp_open eq 0}selected{/if}>不包括运费</option>
            </select>
            <div class='fv2Tip'>税点计算是否包括运费</div>
        </div>
    </div>

</form>

<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary" id='saveBtn' style="width:150px" href="javascript:;">保存设置</a>
</div>

{include file='../__footer.tpl'} 