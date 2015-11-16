
DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function($, util, fancyBox, dataTables) {
    $(function() {
        util.loadOrderStatNums();
        ajaxLoadOrderlist(util);
    });
});

function ajaxLoadOrderlist(util) {
    $('#orderlist').load('?/Wdmin/ajaxLoadOrderlist/page=0&status=refunded', function(r) {
        if (r === '0') {
            util.listEmptyTip();
        } else {
            $('.wshop-empty-tip').remove();
            $('.dTable').dataTable(DataTableConfig).api();
            // 查看快递信息
            fnFancyBox('.od-list-pdinfo');
        }
    });
}