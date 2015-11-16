
/* global shoproot, DataTableConfig */

var dT;

DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner', 'jUploader', 'bootstrap', 'datetimepicker'], function ($, util, fancyBox, dataTables, Spinner, jUploader) {

    $.datetimepicker.setLocale('zh');

    $('#stime').datetimepicker({
        format: 'Y-m-d'
    });
    $('#etime').datetimepicker({
        format: 'Y-m-d'
    });

    util.loadOrderStatNums();

    // 加载订单列表
    ajaxLoadOrderlist(util, listLoadCallback);
    $('#month-select').on('change', function () {
        ajaxLoadOrderlist(util, listLoadCallback);
    });

    // 《新》导出按钮
    $('#confirm-export').click(function () {
        var stime = $('#stime').val();
        var etime = $('#etime').val();
        var otype = $('#otype').val();
        if (stime !== '' && etime !== '') {
            $('#modal_export_orders').modal('hide');
            window.open(shoproot + '?/wOrder/order_exports/stime=' + stime + '&etime=' + etime + '&otype=' + otype);
        } else {
            util.Alert('请选择时间');
        }
    });

    function listLoadCallback() {
        fnFancyBox('.various', function () {
            // 发货按钮点击
            $('#despatchBtn').unbind('click').bind('click', function () {
                var orderId = parseInt($(this).attr('data-orderid'));
                var despatchExpressCode = $('#despatchExpressCode').val();
                var expressCompany = $('#expressCompany').val();
                if (despatchExpressCode === "") {
                    // 必须填入单号
                    $('#despatchExpressCode').addClass('shake').css('border-color', '#900');
                    setTimeout(function () {
                        $('#despatchExpressCode').removeClass('shake');
                    }, 500);
                } else {
                    // 发货走起
                    $('.fancybox-skin').eq(0).append('<div id="iframe_loading" style="top:0;background:rgba(255,255,255,0.7);"></div>');
                    Spinner.spin($('#iframe_loading').get(0));
                    // loading
                    $.post('?/Order/ExpressReady/', {
                        'orderId': orderId,
                        'ExpressCode': despatchExpressCode,
                        'expressCompany': expressCompany,
                        'expressStaff': $('#expressStaff').val()
                    }, function (res) {
                        Spinner.stop();
                        $('#iframe_loading').remove();
                        // loading stop
                        if (res === "1") {
                            util.Alert('发货成功,您可在微店设置中设置快递公司~');
                            dT.row($('#order-exp-' + orderId)).remove().draw();
                            $.fancybox.close();
                        } else {
                            util.Alert('发货失败，系统错误！');
                        }
                    });
                }
            });
        });
    }
});

function ajaxLoadOrderlist(util, callback) {
    $('#orderlist').load('?/Wdmin/ajaxLoadOrderlist/page=0&month=', function (r) {
        if (r === '0') {
            util.listEmptyTip();
        } else {
            if (callback !== undefined) {
                callback();
            }
            dT = $('.dTable').dataTable(DataTableConfig).api();
            var button = $('.button-set').clone(false);
            $('#DataTables_Table_0_filter').append(button);
            button.removeClass('hidden');
            $('.wshop-empty-tip').remove();
        }
    });
}
