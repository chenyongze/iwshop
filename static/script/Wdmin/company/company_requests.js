
DataTableConfig.order = [[3, 'desc']];
requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function($, util, fancyBox, dataTables) {
    $(function() {
        var dt = $('.dTable').dataTable(DataTableConfig).api();
        $('.company-req-p').click(function() {
            var node = $(this);
            if (confirm('确定通过审核？')) {
                var id = parseInt($(this).attr('data-id'));
                if (id > 0) {
                    $.get(shoproot + '?/Company/companyReqPass/id=' + id, function(r) {
                        if (r > 0) {
                            util.Alert('操作成功');
                            dt.row(node.parents('tr')).remove().draw();
                        } else {
                            util.Alert('操作失败');
                        }
                    });
                }
            }
        });
        $('.company-req-np').click(function() {
            if (confirm('确定不通过审核？')) {
                var node = $(this);
                var id = parseInt($(this).attr('data-id'));
                if (id > 0) {
                    $.get(shoproot + '?/Company/companyReqPass/id=' + -id, function(r) {
                        if (r > 0) {
                            util.Alert('操作成功');
                            dt.row(node.parents('tr')).remove().draw();
                        } else {
                            util.Alert('操作失败');
                        }
                    });
                }
            }
        });
    });
});