'use strict';

/* global angular */

var services = angular.module('Order.services', []);

services.factory('Order', ['$http', function ($http) {
        return {
            /**
             * 获取商品信息
             * @param {type} p
             * @returns {undefined}
             */
            getInfo: function (p) {
                return $http.get('?/wOrder/getOrderInfo/', {
                    params: p
                }).error(function (ret) {
                    _process_error(ret);
                });
            },
            /**
             * 获取商品列表
             * @param {type} p
             * @returns {unresolved}
             */
            getList: function (p) {
                return $http.get('?/wOrder/getOrderList/', {
                    params: p
                }).error(function (ret) {
                    _process_error(ret);
                });
            },
            /**
             * 获取订单统计
             * @returns {unresolved}
             */
            getOrderStatnums: function (p) {
                return $http.get('?/wOrder/ajaxGetOrderStatnums/', {
                    params: p
                }).error(function (ret) {
                    _process_error(ret);
                });
            },
            /**
             * 删除订单
             * @param {type} p
             * @returns {unresolved}
             */
            deleteOrder: function (p) {
                return $http.post('?/wOrder/deleteOrder/', $.param(p), {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                }).error(function (ret) {
                    _process_error(ret);
                });
            },
            /**
             * 快递发货
             * @param {type} p
             * @returns {unresolved}
             */
            expressSend: function (p) {
                return $http.post('?/wOrder/expressSend/', $.param(p), {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                }).error(function (ret) {
                    _process_error(ret);
                });
            },
            /**
             * 获取快递公司列表
             * @param {type} p
             * @returns {unresolved}
             */
            getExpressCompanys: function (p) {
                return $http.get('?/wOrder/getExpressCompanys/', {
                    params: p
                }).error(function (ret) {
                    _process_error(ret);
                });
            },
            /**
             * 获取快递人员列表
             * @param {type} p
             * @returns {unresolved}
             */
            getExpressStaffs: function (p) {
                return $http.get('?/wOrder/getExpressStaff/', {
                    params: p
                }).error(function (ret) {
                    _process_error(ret);
                });
            },
            /**
             * 手动确认收货
             * @param {type} p
             * @returns {unresolved}
             */
            confirmOrder: function (p) {
                return $http.post('?/Order/confirmExpress/', $.param(p), {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                }).error(function (ret) {
                    _process_error(ret);
                });
            }
        };
    }]);