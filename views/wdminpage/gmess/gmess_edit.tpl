{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/gmess/gmess_edit.js</i>
<div style='margin-bottom: 40px;padding-top: 20px;padding-right: 20px;'>
    {if $ed}
        <input type="hidden" value="{$g.id}" id="gid" /> 
    {/if}
    <input type="hidden" value="{if $ed}edit{else}add{/if}" id="mod" /> 
    <input type="hidden" value="{$docroot}?/WdminPage/gmess_list/" id="http_referer" /> 
    <div class='clearfix'>
        <div style="float:right;">
            <div id="js_appmsg_preview" class="appmsg_content">
                <div id="appmsgItem1" data-fileid="" data-id="1" class="js_appmsg_item">
                    <h4 class="appmsg_title"><a href="javascript:;">{if $ed}{$g.title}{else}标题{/if}</a></h4>
                    <div class="appmsg_info">
                        <em class="appmsg_date"></em>
                    </div>
                    <a class="appmsg_thumb_wrp pd-image-sec{if $ed and $g.catimg neq ''} ove0{/if}" id="thumbUp">
                        <img class="js_appmsg_thumb appmsg_thumb" src="{if $ed}{$docroot}uploads/gmess/{$g.catimg}{/if}" id="appmsimg-preview" {if $ed and $g.catimg neq ''}{else}style="display: none;"{/if}>
                    </a>
                    <p class="appmsg_desc">{if $ed}{$g.desc}{/if}</p>
                </div>
                <p class='tTip'>建议尺寸：900像素 * 500像素</p>
            </div>
        </div>
        <form id="uploadForm" style="margin-right: 360px;">
            <a id="fileSubmit" style="display: none"></a>
            <input name="catimg" id="catimgpath" value="{if $ed}{$g.catimg}{/if}" type="hidden" />
            <div style="margin-left: 22px;">
                <p class="Thead">图文标题</p>
                <div class="gs-text">
                    <input type="text" name="title" value='{if $ed}{$g.title}{/if}' id="gs-form-title" />
                </div>
                <p class="Thead">图文摘要</p>
                <span class="frm_textarea_box"><textarea class="js_desc frm_textarea" id="gs-form-desc" name="desc">{if $ed}{$g.desc}{/if}</textarea></span>
            </div>
            <div id="editorContain">
                <p class="Thead">正文内容</p>
                <script id="ueditorp" name="content" type="text/plain" style="width: 99.5%">{if $ed}{$g.content}{/if}</script>
            </div>
        </form>
    </div>    
</div>
<div class="fix_bottom fixed">
    <a id="save_gmess_btn" href="javascript:;" class="wd-btn primary" data-id="{$g.id}">保存</a>
    {if $ed}<a class="wd-btn delete pd-del-btn" id="del_gmess_btn" data-id="{$g.id}">删除</a>{/if}
    <a onclick="location.href = $('#http_referer').val();" class="wd-btn default">返回</a>
</div>
{include file='../__footer.tpl'} 