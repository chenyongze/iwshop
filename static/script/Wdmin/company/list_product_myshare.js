requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'ztree', 'ztree_loader'], function($, util, fancyBox, dataTables, ztree, treeLoader) {
    $(function() {
        var dT = $('.dTable').dataTable(DataTableConfig).api();

        fnFancyBox('.pd-qrcodebtn', function() {

        });

    });
});