/* global angular */

var app = angular.module('ngApp', ['Order.services', 'Util.services']);

app.controller('orderController', function ($scope, Order, Util) {

    // 订单统计数据
    $scope.statdata;
    // 订单列表
    $scope.orderList;
    // 搜索类型
    $scope.search_type = 0;
    // 列表参数
    $scope.params = {
        page: 0,
        page_size: 40,
        status: 'all',
        serial_number: ''
    };
    var loading = false;
    // 操作订单Id
    $scope.order_id = 0;
    // 操作按钮
    $scope.btn;
    // 分页总数
    $scope.listcount = 0;
    // 初始化
    $scope.init = false;
    // 快递单号
    $scope.express_code = '';

    // 拉取订单统计数据
    Order.getOrderStatnums().success(function (r) {
        $scope.statdata = r;
    });

    $scope.$watch('listcount', function (newValue, oldValue) {
        if (newValue > 0) {
            fnInitPager();
            $scope.init = true;
        }
    });

    /**
     * 初始化分页
     * @returns {undefined}
     */
    function fnInitPager() {
        if ($scope.listcount > 0) {
            Util.initPaginator(Math.ceil($scope.listcount / $scope.params.page_size), fnOnPageChange);
        } else {
            $('.navbar-fixed-bottom').hide();
        }
    }

    /**
     * 分页变化
     * @param {type} page
     * @param {type} type
     * @returns {undefined}
     */
    function fnOnPageChange(page, type) {
        if ($scope.init) {
            $('body').animate({scrollTop: '0'}, 200);
            $scope.params.page = page - 1;
            fnGetList();
        }
    }

    function fnGetList() {
        Order.getList($scope.params).success(function (r) {
            $scope.listcount = r.ret_msg.count;
            $scope.orderList = r.ret_msg.list;
        });
    }

    Order.getInfo({
        id: 221
    }).success(function (r) {
        $scope.orderInfo = r.ret_msg;
    });

    // 查看订单
    $('#modal_order_view').on('show.bs.modal', function (event) {
        $scope.btn = $(event.relatedTarget);
        $scope.order_id = parseInt($scope.btn.data('id'));
        $scope.express_code = '';
        Order.getInfo({
            id: $scope.order_id
        }).success(function (r) {
            $scope.orderInfo = r.ret_msg;
        });
    });

    // 删除订单
    $('#modal_order_delete').on('show.bs.modal', function (event) {
        $scope.btn = $(event.relatedTarget);
        $scope.order_id = parseInt($scope.btn.data('order_id'));
    });

    // 删除订单确认
    $('#modal_order_delete .btn-danger').click(function () {
        var btn = $(this);
        btn.parents('tr').remove();
        if (!loading) {
            loading = true;
            btn.html('删除中');
            // 删除订单
            Order.deleteOrder({
                order_id: $scope.order_id
            }).success(function (r) {
                $('#modal_order_delete').modal('hide');
                loading = false;
                btn.html('删除');
                if (r.ret_code === 0) {
                    $scope.btn.parents('tr').remove();
                    Util.alert('删除成功');
                } else {
                    Util.alert('删除失败', true);
                }
            });
        }
    });

    $('#list-reload').click(fnGetList);

    // 订单状态选项
    $('#order-status a').click(function () {
        $scope.params.status = $(this).data('type');
        $('#order-status-label').html($(this).html());
        $scope.params.page = 0;
        fnGetList();
    });

    // 房源搜索类型选项
    $('#search-type a').click(function () {
        $scope.search_type = +$(this).data('type');
        $('#search-type-label').html($(this).html());
    });

    // 搜索框回车
    $('#search-key').bind('keyup', function (e) {
        if (e.keyCode === 13) {
            $('#search-button').click();
        }
    });

    // 房源搜索按钮
    $('#search-button').click(function () {
        $scope.params.serial_number = '';
        switch ($scope.search_type) {
            case 0:
                $scope.params.serial_number = $('#search-key').val();
                break;
            case 1:
                $scope.params.phone = $('#search-key').val();
                break;
            case 2:
                $scope.params.uname = $('#search-key').val();
        }
        fnGetList();
    });

    // 确认收货
    $('#order-confirm-btn').click(function () {
        if (confirm('你确认买家已经收到货了吗')) {
            Order.confirmOrder({
                orderId: $scope.order_id
            }).success(function (r) {
                if (r > 0) {
                    $('#modal_order_view').modal('hide');
                    Util.alert('确认收货成功！');
                    // 刷新列表
                    fnGetList();
                } else {
                    Util.alert('确认收货失败！', true);
                }
            });
        }
    });

    // 订单发货
    $('#order-express-btn').click(function () {
        if (loading) {
            return false;
        }
        if ($scope.express_code === '') {
            return Util.alert('请输入正确的快递单号');
        }
        Order.expressSend({
            orderId: $scope.order_id,
            expressCode: $scope.express_code,
            expressCompany: $scope.express_company,
            expressStaff: $scope.express_staff
        }).success(function (r) {
            $('#modal_order_view').modal('hide');
            loading = false;
            if (r.ret_code === 0) {
                Util.alert('发货成功');
                // 刷新列表
                fnGetList();
            } else {
                Util.alert('发货失败', true);
            }
        });
    });

    // 获取快递公司列表
    Order.getExpressCompanys().success(function (r) {
        $scope.express_company = r.ret_msg[0].code;
        $scope.express_companys = r.ret_msg;
    });

    $scope.express_staff = '';

    // 获取快递人员列表
    Order.getExpressStaffs().success(function (r) {
        $scope.express_staffs = r.ret_msg;
    });

    fnGetList();
});