{include file='../__header.tpl'}
<i id="scriptTag">page_orders_toexpress</i>
<link href="{$docroot}static/less/jquery.datetimepicker.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
<link href="{$docroot}static/css/bootstrap/bootstrap.css" type="text/css" rel="Stylesheet" />
<div id="orderlist"></div>
<div class="button-set hidden">
    <a class="button gray" href="javascript:;" onclick='location.reload()'>刷新</a>
    <a class="button" data-toggle="modal" data-target="#modal_export_orders">导出数据</a>
</div>
{include file='../modal/modal_export_orders.html'}
{include file='../__footer.tpl'} 