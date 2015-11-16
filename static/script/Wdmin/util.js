
// 多选开关
DataTableMuli = false;
DataTableSelect = false;

define(['jquery', 'Spinner'], function ($, Spinner) {

    var Util = {};

    /**
     * loading animate
     * @type @exp;Object@call;onew
     */
    window.Loading = Object.onew({
        start: function (id) {
            Spinner.spin($(id).get(0));
            id = null;
        },
        finish: function () {
            Spinner.spin().stop();
        }
    });

    window.wpLoading = function (id) {
        id = id || "#main-mid";
        Loading.start(id);
        id = null;
    };

    /**
     * datatable事件监听
     * @returns {undefined}
     */
    Util.dataTableLis = function (tableId, sel) {
        // 绑定节点集合
        var n;
        // 默认为.dTable
        tableId = tableId || '.dTable';
        // 是否可以选中 默认false
        DataTableSelect = sel || DataTableSelect;
        // 节点还是queryStr
        n = (typeof tableId === 'string') ? $(tableId + ' tbody tr') : $(tableId);
        n.unbind('click').click(function () {
            var node = $(this);
            if (DataTableSelect) {
                var cb = node.find('input:checkbox')[0];
                if (!DataTableMuli) {
                    // 单选
                    node.parent().find('tr.click').removeClass('click');
                    node.addClass('click');
                    node.parent().find('input:checked').each(function () {
                        this.checked = false;
                    });
                    if (cb) {
                        cb.checked = true;
                    }
                } else {
                    // 多选
                    if ($(this).hasClass('click')) {
                        $(this).toggleClass('click');
                        if (cb) {
                            cb.checked = false;
                        }
                    } else {
                        $(this).addClass('click');
                        if (cb) {
                            cb.checked = true;
                        }
                    }
                }
                cb = null;
            }
            $('.button.del,.button.edit').css('display', 'inline-block');
        }).mouseover(function () {
            $(this).addClass('hover');
        }).mouseout(function () {
            $(this).removeClass('hover');
        });
        if (DataTableSelect && DataTableMuli) {
            if (typeof tableId !== 'object') {
                $(tableId + ' thead tr .checkAll').unbind('click').click(function () {
                    var n = this;
                    $('tbody tr', $(this).parents('table')).find('input:checkbox').each(function (i, node) {
                        node.checked = n.checked;
                    });
                    if (n.checked) {
                        $('tbody tr', $(this).parents('table')).addClass('click');
                    } else {
                        $('tbody tr', $(this).parents('table')).removeClass('click');
                    }
                });
            }
        }
        tableId = null;
        n = null;
    };

    window.dataTableLis = Util.dataTableLis;

    Util.resize = function () {

    };
    
    /**
     * Alert
     * @param {type} message
     * @param {type} warn
     * @param {type} callback
     * @returns {undefined}
     */
    Util.Alert = function (message, warn, callback) {
        warn = warn || false;
        var node = $('<div id="__alert__"></div>');
        if (warn) {
            node.addClass('warn');
        } else {
            node.removeClass('warn');
        }
        node.html(message);
        $('body').append(node);
        node.css('left', ($('body').width() - node[0].clientWidth) / 2 + 'px').slideDown();
        window.setTimeout(function () {
            node.slideUp(300, function () {
                if (typeof callback === 'function') {
                    callback();
                }
                $('#__alert__').remove();
            });
        }, 3000);
    };

    Util.dataTableLoading = function (query) {
        query = query || '.dTable';
        // <tr><td colspan="6" class="datatableLoading"> </td></tr>
        $('tbody', query).append('<tr class="rmd"><td colspan="6" class="datatableLoading"> </td></tr>');
    };

    Util.dataTableLoadingEnd = function (query, never) {
        query = query || '.dTable';
        never = never || false;
        $('.rmd', query).remove();
        if (!never)
            scrolling = false;
    };

    Util.scrollBottom = function (callback, offset) {
        if (typeof scrolling === 'undefined') {
            scrolling = false;
        }
        offset = offset || 150;
        $(window).scroll(function () {
            totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()) + offset;
            if ($(document).height() <= totalheight && !scrolling) {
                scrolling = true;
                callback();
            }
        });
    };

    Util.listEmptyTip = function (node) {
        $('body').css('min-height', $(window).height());
        $('.dTable,#list').css('margin', '0');
        node = node || 'body';
        $(node).css('position', 'relative').append('<div class="wshop-empty-tip" onclick="parent.reloadPage();"></div>');
        node = null;
    };

    Util.removeEmptyTip = function () {
        $('.wshop-empty-tip').hide();
    };

    /**
     * 获取订单分类统计数据
     * @returns {undefined}
     */
    Util.loadOrderStatNums = function () {
        $.get(shoproot + '?/WdminAjax/ajaxGetOrderStatnums', function (Json) {
            // cap-nav-item
            var capNavs = $('#subnav4 .cap-nav-item', parent.window.document);
            capNavs.eq(0).find('b').html('(' + Json.payed + ')');
            capNavs.eq(1).find('b').html('(' + Json.delivering + ')');
            capNavs.eq(2).find('b').html('(' + Json.unpay + ')');
            capNavs.eq(3).find('b').html('(' + Json.refunded + ')');
            capNavs.eq(4).find('b').html('(' + Json.received + ')');
            capNavs.eq(5).find('b').html('(' + Json.canceled + ')');
            capNavs.eq(6).find('b').html('(' + Json.all + ')');
        });
    };

    /**
     * 获取商品分类统计数据
     * @returns {undefined}
     */
    Util.loadProductStatNums = function () {
        $.get(shoproot + '?/WdminAjax/ajaxGetProductStatnums', function (Json) {
            // cap-nav-item
            var capNavs = $('#subnav5 .cap-nav-item', parent.window.document);
            capNavs.eq(0).find('b').html('(' + Json.pdcount + ')');
            capNavs.eq(1).find('b').html('(' + Json.cacount + ')');
            capNavs.eq(2).find('b').html('(' + Json.spcount + ')');
            capNavs.eq(3).find('b').html('(' + Json.secount + ')');
            capNavs.eq(4).find('b').html('(' + Json.brcount + ')');
            capNavs.eq(5).find('b').html('(' + Json.pdcount2 + ')');
        });
    };

    /**
     * 获取代理统计数据
     * @returns {undefined}
     */
    Util.loadCompanyStatNums = function () {
        $.get(shoproot + '?/WdminAjax/ajaxGetCompanyStatNums', function (Json) {
            // cap-nav-item
            var capNavs = $('#subnav8 .cap-nav-item', parent.window.document);
            capNavs.eq(0).find('b').html('(' + Json.count1 + ')');
            capNavs.eq(1).find('b').html('(' + Json.count2 + ')');
            capNavs.eq(2).find('b').html('(' + Json.count3 + ')');
        });
    };

    Util.onresize = function (func) {
        if (typeof func === 'function') {
            $(window).on('resize', func);
            func();
        }
    };

    /**
     * 
     * @param {type} dT dataTable .api句柄
     * @returns {undefined}
     */
    Util.pdDeleteListen = function (dT) {
        /**
         * 商品删除按钮监听函数
         */
        $('.pd-del-btn').unbind('click').click(function () {
            var tR = $(this).parent().parent();
            if (confirm('你确定要删除这个产品吗')) {
                $.post(shoproot + '?/WdminAjax/deleteProduct/', {
                    id: parseInt($(this).attr('data-product-id'))
                }, function (res) {
                    if (parseInt(res) > 0) {
                        if (dT === undefined) {
                            // 如果是商品编辑内部删除 返回上一个列表
                            location.href = $('#http_referer').val()
                        } else {
                            tR.fadeOut('normal', function () {
                                dT.row(tR).node().remove();
                            });
                        }
                    } else {
                        alert('删除失败');
                    }
                });
            }
        });
    };

    Util.confirmExp = function (orderId) {
        orderId = parseInt(orderId);
        if (orderId > 0) {
            if (confirm('你确认该订单已经收货了吗?')) {
                $.post(shoproot + '?/Order/confirmExpress', {orderId: orderId}, function (res) {
                    res = parseInt(res);
                    if (res > 0) {
                        Util.Alert('确认收货成功！');
                        window.location.reload();
                    } else {
                        Util.Alert('确认收货失败！', true);
                        bugNotify('确认收货失败！');
                    }
                });
            }
        }
    };

    Util.dataTableConfig = {
        "bPaginate": false,
        "bLengthChange": false,
        "iDisplayLength": 6000,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false,
        "fnInitComplete": function () {
            dataTableLis();
            $('.dataTables_filter').addClass('clearfix');
            $('.search-w-box input').attr('placeholder', '输入搜索内容');
        }
    };

    /**
     * 回车监听
     * @param {type} node
     * @param {type} callback
     * @returns {undefined}
     */
    Util.keyEnter = function (node, callback) {
        $(node).bind('keydown', function (e) {
            var key = e.which;
            if (key === 13) {
                callback($(this).val());
            }
        });
    };

    /**
     * 图片错误监听
     * @returns {undefined}
     */
    Util.imageError = function () {
        $('img').unbind('error').bind('error', function () {
            $(this).attr('src', 'static/images/icon/iconfont-pic.png');
        });
    };

    return Util;
});

Object.onew = function (o) {
    var F = function (o) {
    };
    F.prototype = o;
    return new F;
};