/* global shoproot */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
define(['jquery'], function ($) {

    var util = {};

    util.log = function (v) {
        console.log(v);
    };

    util.bottomNavSwitch = function (link) {
        $('.bottom_nav div.hover').removeClass('hover');
        $(".bottom_nav div[data-href='" + link + "']").addClass('hover');
    };

    util.fnTouchEndRedirect = function (node, func) {
        $(node).bind('touchstart mousedown', function (event) {
            util.touchNode = $(this);
            $('.hover', $(node).parent()).removeClass('hover');
            $(this).addClass('hover');
            $(this).attr('touchStartTime', (new Date()).getTime());
        });
        $(node).bind('touchend mouseup', function (event) {
            var endTime = (new Date()).getTime();
            if (endTime - parseInt($(this).attr('touchStartTime')) < 70) {
                // 触摸间隔低于70ms，判断为点击，否则忽略
                var href = util.touchNode.attr('href') || util.touchNode.attr('data-href');
                if (href && href !== '') {
                    if (func !== undefined) {
                        func(href, $(this));
                    }
                }
            }
            endTime = null;
            // 取消设备默认点击反馈
            event.preventDefault();
        });
    };

    util.fnTouchEnd = function (node, func) {
        $(node).bind('touchend mouseup', function (event) {
            if (func !== undefined) {
                func($(this));
            }
            // 取消设备默认点击反馈
            event.preventDefault();
        });
    };

    util.onresize = function (func, node) {
        node = node || window;
        node.onresize = func;
        func();
    };

    util.q = function (str) {
        return document.querySelector(str);
    };

    util.searchListen = function () {
        $('.search-w-box').bind('submit', function () {
            var form = this;
            var inp = $('input[type=search]', form);
            if (inp.val() === '') {
                return;
            } else {
                var target = inp.attr('targ');
                target = encodeURIComponent(target + '&searchkey=' + inp.val());
                location.href = shoproot + '?/vSearch/rd/href=' + target + '&searchkey=' + encodeURI(inp.val());
            }
            return false;
        });
    };

    /**
     * 获取配置
     * @param {type} callback
     * @returns {undefined}
     */
    util.getconfig = function (callback) {
        $.post(shoproot + '?/wSettings/ajaxGetSettings/',{}, callback);
    };

    /**
     * 获取运费模板
     * @param {type} callback
     * @returns {undefined}
     */
    util.getExpTemplate = function (callback) {
        $.post(shoproot + '?/wSettings/ajaxGetExpTemplate/',{}, callback);
    };

    return util;
});