
DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'ztree', 'ztree_loader', 'Spinner'], function($, util, fancyBox, dataTables, ztree, treeLoader, Spinner) {
    $(function() {

        var dT = $('.dTable').dataTable(DataTableConfig).api();

        fnFancyBox('#add_enterprise', function() {
            $('#al-com-save').click(function() {
                var name = $('#cname').val();
                var phone = $("#cphone").val();
                $.post('?/Enterprise/ajaxAdd/', {
                    name: name,
                    phone: phone
                }, function(r) {
                    if (r > 0) {
                        util.Alert('添加成功');
                        location.reload();
                    } else {
                        util.Alert('添加失败，系统错误', true);
                    }
                });
            });
        });

        fnFancyBox('.com-ajaxqrcode');

        fnFancyBox('.ent-edit', function() {
            $('#al-com-save').click(function() {
                var name = $('#cname').val();
                var phone = $("#cphone").val();
                var id = $(this).attr('data-id');
                $.post('?/Enterprise/ajaxUpdate/', {
                    name: name,
                    phone: phone,
                    id: id
                }, function(r) {
                    if (r > 0) {
                        util.Alert('编辑成功');
                        location.reload();
                    } else {
                        util.Alert('编辑失败，系统错误', true);
                    }
                });
            });
        });

    });
});