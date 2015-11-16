
/* global shoproot, DataTableConfig */

var dT, posting = false;

DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function ($, util, fancyBox, dataTables) {
    $(function () {
        util.loadOrderStatNums();
        ajaxLoadOrderlist(util);
    });
});

function ajaxLoadOrderlist(util) {
    $('#orderlist').load('?/Wdmin/ajaxLoadOrderlist/page=0&status=canceled', function (r) {
        if (r === '0') {
            util.listEmptyTip();
        } else {
            $('.wshop-empty-tip').remove();
            dT = $('.dTable').dataTable(DataTableConfig).api();
            // 查看快递信息
            fnFancyBox('.pd-list-viewExp');
            // 退款按钮
            fnFancyBox('.orderRefund', function () {
                $('#refundBtn').on('click', function () {
                    var realAmount = parseFloat($('.orderwpa-amount').eq(0).attr('data-amount'));
                    var amount = parseFloat($('#despatchExpressCode').eq(0).val() !== '' ? $('#despatchExpressCode').eq(0).val() : 0);
                    // 金额检查
                    if (amount <= 0 || realAmount < amount) {
                        util.Alert('请填写正确的退款金额!', true);
                        return;
                    }
                    // 发起退款
                    if (!posting && confirm('你确认要退款 ' + parseFloat($('#despatchExpressCode').eq(0).val()) + ' 元吗?')) {
                        var orderId = $(this).attr('data-id');
                        util.Alert('正在处理中...');
                        $('#refundBtn').html('正在处理中...');
                        posting = true;
                        // [HttpPost]
                        $.post(shoproot + '?/Order/orderRefund/', {
                            id: orderId,
                            amount: amount
                        }, function (r) {
                            $('#refundBtn').html('确认退款');
                            posting = false;
                            if (r !== '0') {
                                util.Alert('退款请求已成功提交，请等待微信商户处理', false);
                                dT.row($('#order-exp-' + orderId)).remove().draw();
                                $.fancybox.close();
                            } else {
                                util.Alert('退款处理出错，请联系技术支持', true);
                            }
                        });

                    }
                });
            });
        }
    });
}