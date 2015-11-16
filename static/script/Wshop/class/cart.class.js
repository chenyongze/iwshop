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

define([], function () {
    var storage = window.localStorage;
    var _o = {
        // cartJson对象
        cart: {},
        // 初始化
        init: function () {
            var _d = storage.getItem('cart');
            if (_d) {
                this.cart = eval('(' + storage.getItem('cart') + ')');
            } else {
                this.cart = {};
            }
            this.check();
        },
        add: function (productId, count, priceHashId) {
            eval("var ext = this.cart.p" + productId + "m" + priceHashId);
            var cmd = ext ? ' +=' : ' =';
            eval("this.cart.p" + productId + "m" + priceHashId + cmd + count);
            this.save();
        },
        del: function (productId, priceHashId) {
            eval("delete this.cart.p" + productId + "m" + priceHashId);
            this.save();
        },
        count: function () {
            var c = 0;
            for (var k in this.cart) {
                c += this.cart[k];
            }
            return c;
        },
        clear: function () {
            this.cart = {};
            storage.setItem('cart', '{}');
            storage.removeItem('tmporder');
            storage.removeItem('carthash');
        },
        save: function () {
            storage.setItem('cart', JSON.stringify(this.cart));
        },
        set: function (mhash, count) {
            eval("this.cart." + mhash + "=" + count);
            this.save();
        },
        check: function () {
            var self = this;
            $.post(shoproot + '?/Order/checkCart/', {
                data: storage.getItem('cart')
            }, function(r){
                for(var ri in r){
                    eval("delete self.cart." + r[ri]);
                }
                self.save();
            });
        }
    };
    _o.init();
    return _o;
});