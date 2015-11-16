{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/company/list_product_myshare.js</i>
<input type="hidden" id="cat" value="{$cat}" />
<div{if !$iscom} style="margin-bottom: 40px;"{/if}>
    <table class="dTable">
        <thead>
            <tr>
                <th>略缩图</th>
                <th style='width:300px'>产品名称</th>
                <th>产品价格</th>
                <th>产品分类</th>
                <th>产品风格</th>
                <th>点击量</th>
                <th>购买数</th>
                <th>转化率</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            {include file='../products/iframe_list_products_section_com.tpl'}
        </tbody>
    </table>
</div>
{if !$iscom}
    <div class="fix_bottom" style="position: fixed">
        <a class="wd-btn primary" id='add_cate_product' style="width:150px" href="?/WdminPage/iframe_alter_product/mod=add&catid={$cat}">添加产品</a>
    </div>
{/if}
{include file='../__footer.tpl'} 