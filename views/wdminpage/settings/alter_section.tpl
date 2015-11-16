{include file='../__header.tpl'}
<link href="{$docroot}static/less/jquery.datetimepicker.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
<i id="scriptTag">{$docroot}static/script/Wdmin/settings/alter_section.js</i>

<form style="padding:15px 20px;padding-bottom: 70px;" id="settingFrom">

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>板块名称</span>
        </div>
        <div class="fv2Right">
            <input type="text" class="gs-input" id="name" value="{$sec.name}" placeholder="请输入板块名称" autofocus/>
            <div class='fv2Tip'>板块名称，显示在板块顶部</div>
        </div>
    </div>

    <!-- 分类对应 -->
    <div class="fv2Field typeHash clearfix" id="hashCat">
        <div class="fv2Left">
            <span>选择分类</span>
        </div>
        <div class="fv2Right">
            <select id="pd-cat-select" style="color:#666">
                {foreach from=$categorys item=cat1}
                    <option value="{$cat1.dataId}" {if $sec.relid eq $cat1.dataId}selected{/if}>{$cat1.name}</option>
                    {foreach from=$cat1.children item=cat2}
                        <option value="{$cat2.dataId}" {if $sec.relid eq $cat2.dataId}selected{/if}>-- {$cat2.name}</option>
                        {foreach from=$cat2.children item=cat3}
                            <option value="{$cat3.dataId}" {if $sec.relid eq $cat3.dataId}selected{/if}>---- {$cat3.name}</option>
                        {/foreach}
                    {/foreach}
                {/foreach}
            </select>
            <div class='fv2Tip'>滚动图对应的分类</div>
        </div>
    </div>

    <!-- 排序 -->
    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>排序</span>
        </div>
        <div class="fv2Right">
            <input class='gs-input' type="text" name="bsort" id='bsort' onclick="this.select()" value="{$sec.bsort}" />
            <div class='fv2Tip'>数字越大排序越前</div>
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>对应图片</span>
        </div>
        <div class="fv2Right">
            <div class="clearfix">
                <div class="alter-cat-img">
                    <input type="hidden" value="{$sec.banner}" id="banner" />
                    <div id="loading" style="transition-duration: .2s;"></div>
                    <img id="catimage" src="{if $sec.banner neq ''}{$docroot}uploads/banner/{$sec.banner}{/if}" />
                    {if $sec.banner eq ''}
                        <div style='line-height: 100px;color:#777;' class='align-center' id="cat_none_pic">无图片</div>
                    {/if}
                    <div class="align-center top10">
                        <a class="wd-btn primary" id="upload_banner" href="javascript:;">更换图片</a>
                    </div>
                </div>
            </div>
            <div class='fv2Tip'>滚动图对应要显示的图片 建议尺寸600&times;290</div>
        </div>
    </div>

    <!-- 商品对应 -->        
    <div class="fv2Field typeHash clearfix" id="hashProduct" style="max-width:100%;">
        <div class="fv2Left">
            <span>选择产品</span>
        </div>
        <div class="fv2Right">
            <a id="sProduct" href="?/FancyPage/ajaxSelectProduct/" class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" style="margin:0;width:389px;" data-id="">选择产品</a>
            <div class='fv2Tip hidden' id="spdCount">已选择100个产品</div>
            <div id="ProductItem" class="clearfix">
                {if $products}
                    {include file='../fancy/ajaxPdBlocks.tpl'}
                {/if}
            </div>
            <div class='fv2Tip' id="spdTip">请点击选择产品</div>
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>开放时间</span>
        </div>
        <div class="fv2Right">
            <div class="clearfix">
                <div style="width: 47%;float: left;">
                    <input type="text" class="gs-input" id="dt1" value="{$sec.ftime}" placeholer="点击设置开始时间" autofocus/>
                </div>
                <div style="width: 6%;float:left;text-align: center;line-height: 32px;text-indent: 3px;"> - </div>
                <div style="width: 47%;float: right;">
                    <input type="text" class="gs-input" id="dt2" value="{$sec.ttime}" placeholer="点击设置结束时间" autofocus/>
                </div>
            </div>
            <div class='fv2Tip'>板块名称，显示在板块顶部</div>
        </div>
    </div>

</form>

<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary" id='saveBtn' data-id='{$sec.id}' href="javascript:;">{if $sec.id > 0}保存{else}添加{/if}</a>
    <a onclick="history.go(-1)" class="wd-btn default">返回</a>
</div>

{include file='../__footer.tpl'} 