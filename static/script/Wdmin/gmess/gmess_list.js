
DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner'], function($, util, fancyBox, dataTables, Spinner) {

    $(function() {
        var dt = $('.dTable').dataTable(DataTableConfig).api();
        $('.del_gmess_btn').click(function() {
            var node = $(this);
            if (confirm('要删除这个素材吗？和这个素材相关的文章将失效')) {
                $.post('?/Gmess/ajaxDelByMsgId/', {msgid: $(this).attr('data-id')}, function(r) {
                    if (r.status > 0) {
                        dt.row(node.parents('tr')).remove().draw();
                        util.Alert('删除成功！');
                    } else {
                        util.Alert('删除失败！', true);
                    }
                });
            }
        });
        $('.gmessView').bind('click', function(event) {
            parent.window.open($(this).attr('data-href'))
            event.stopPropagation();
        });
    });

});