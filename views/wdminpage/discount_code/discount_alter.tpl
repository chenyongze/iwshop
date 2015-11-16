<div style="width:300px">
    <div class="gs-label">关键字</div>
    <div class="gs-text">
        <input type="text" value="{$ret.keywords}" id="gkeyword" autofocus/>
    </div>
    <div class="gs-label">优惠价格</div>
    <div class="gs-text">
        <input type="text" value="{$ret.code_discount}" id="gdiscount" autofocus/>
    </div>
    <div class="gs-label">消息模板</div>
    <textarea id="mpdcont" cols="4" class="mpdcont" style="width: 95%;" placeholder="__A__表示优惠码，__B__表示优惠价格">{$ret.template}</textarea>
    <div class="center" style="margin-top: 15px">
        <a id="save_btn" href="javascript:;" data-id="{$ret.id}" class="wd-btn primary">保存</a>
    </div>
</div>