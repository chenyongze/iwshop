
DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'datatables'], function($, util, dataTables) {
    $(function() {
        var dt = $('.dTable').dataTable(DataTableConfig).api();
    });
});