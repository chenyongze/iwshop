
DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function($, util, fancyBox, dataTables) {
    $(function() {

        DataTableSelect = true;
        DataTableMuli = true;
        
        $('#month-select').on('change', function() {
            ajaxLoadOrderlist(util);
        });
        ajaxLoadOrderlist(util);
    });
});

function ajaxLoadOrderlist(util) {
    $('#orderlist').load('?/Wdmin/ajaxLoadOrderlist/page=0&status=payed&export=true', function(r) {
        if (r === '0') {
            util.listEmptyTip();
        } else {
            $('.wshop-empty-tip').remove();
            $('.dTable').dataTable(DataTableConfig).api();
        }
    });
}