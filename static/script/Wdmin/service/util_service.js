'use strict';

/* global angular */

var services = angular.module('Util.services', []);

services.factory('Util', ['$http', function ($http) {
        return {
            /**
             * Alert
             * @param {type} message
             * @param {type} warn
             * @param {type} callback
             * @returns {undefined}
             */
            alert: function (message, warn, callback) {
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
            },
            /**
             * 分页插件
             * @param {type} total
             * @param {type} func
             * @returns {undefined}
             */
            initPaginator: function (total, func) {
                $(".pagination").jqPaginator({
                    totalPages: total,
                    onPageChange: func
                });
            }
        };
    }]);