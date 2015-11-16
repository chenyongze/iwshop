/* global wx, shoproot, parseFloat */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

require(['config'], function (config) {

    require(['util', 'jquery'], function (util, $) {

        var currentOrderpage = 0;
        var orderLoading = false;
        var orderLoadingLock = false;
        var totalheight;

        // orderlist列表页面
        if ($('#uc-orderlist').length > 0) {
            // init list
            loadOrderList(currentOrderpage);
            // onscroll bottom
            $(window).scroll(function () {
                totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()) + 150;
                if ($(document).height() <= totalheight && !orderLoading) {
                    //加载数据
                    loadOrderList(++currentOrderpage);
                }
            });
        }

        $('.uc-order-sort').unbind('click').click(function () {
            currentOrderpage = -1;
            orderLoading = false;
            orderLoadingLock = false;
            $('#status').val($(this).attr('data-status'));
            $('.uc-order-sort.hover').removeClass('hover');
            $(this).addClass('hover');
            loadOrderList(currentOrderpage);
        });

        // Ajax load Order list 
        function loadOrderList(page) {
            if (!orderLoadingLock) {
                page = parseInt(page);
                if (page === -1) {
                    page = 0;
                    $("#uc-orderlist").html('');
                }
                // request uri
                orderLoading = true;
                $('#list-loading').show();
                // [HttpGet]
                $.get('?/Uc/ajaxOrderlist/page=' + page + '&status=' + $('#status').val(), function (HTML) {
                    orderLoading = false;
                    if (HTML === '' && page === 0) {
                        // 什么都没有
                        $("#uc-orderlist").append('<div class="emptyTip">暂无数据</div>');
                    } else if (HTML !== '') {
                        if (page === 0) {
                            $("#uc-orderlist").html(HTML);
                        } else {
                            $("#uc-orderlist").append(HTML);
                        }
                    } else {
                        orderLoadingLock = true;
                    }
                    $('#list-loading').hide();
                });
            }
        }

    });

});