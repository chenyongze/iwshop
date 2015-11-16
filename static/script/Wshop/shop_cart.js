/* global address_item_click, CryptoJS, wx, WeixinJSBridge, shoproot, addrsignPackage, address_save, o */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

// order id 生成标记
window.orderId = false;
// 支付完成标记
window.payed = false;
// 收货地址加载标记
window.addressloaded = false;
// 收货地址
window.expressData = {};
// lock
window.orderCreateLock = false;
// 初始运费
window.yunfeiInitial = 6.95;

require(['config'], function (config) {

    require(['util', 'jquery', 'Spinner', 'Cart'], function (util, $, Spinner, Cart) {

        if ($('#promAva').val() < 1 && $('#promId').val() !== '') {
            alert('您已参与过秒杀活动');
            window.history.go(-1);
        }

        var o = {};

        o.cartData = '';

        o.isProm = false;

        o.promCount = 1;

        // 促销商品Id
        o.promId = false;

        // 促销限数
        o.promLimit = false;

        // 使用的红包
        o.envsId = false;

        // 没有选择红包 询问
        o.envsAsked = false;

        // 是否有可用红包
        o.envsAva = false;

        o.envsReq = 0;

        o.envsDis = 0;

        // 是否开具发票
        o.isReci = false;

        // 发票税点
        o.ReciTex = 0;

        // 税点是否包括运费
        o.TexIncludeExp = false;

        // 税
        o.Tex = 0;

        // localStorage对象
        o.Storage = window.localStorage;

        // 运费总额
        o.ExpFee = 0;

        // 运费起点
        o.ExpFeeInitial = 0;

        // 运费模板
        o.ExpFeeTemplate = {};

        // 订单总额
        o.TotalFee = 0;

        // 订单实际额 不包括运费、优惠
        o.ActalFee = 0;

        // 系统设置
        o.settings = {};

        /**
         * 计算订单金额
         * @param {type} amount
         * @returns {undefined}
         */
        o.countAmount = function (amount) {
            $('#order_amount').html('&yen;' + (amount !== undefined ? amount : countOrderAmount()));
        };

        // 加载购物车数据
        o.loadCartData = function () {
            o.promId = $('#promId').val();
            if (o.promId !== '') {
                // 如果是促销购物车
                o.cartData = '{"' + o.promId + '":' + o.promCount + '}';
                o.isProm = true;
            } else {
                // 普通购物车
                o.cartData = o.Storage.getItem('cart');
            }
            if (o.cartData === undefined || o.cartData === null || o.cartData === '{}') {
                // 购物车为空
                fnEmptyCartTip();
            } else {
                Spinner.spin($('#orderDetailsWrapper').get(0));
                // [HttpPost]
                $.post(shoproot + '?/vProduct/cartData/', {
                    data: o.cartData.toString()
                }, function (Res) {
                    Spinner.stop();
                    $('#orderDetailsWrapper').html(Res);
                    // 数量变化监听
                    fnPdCountChangerLis();
                    //购物车删除按钮点击
                    $('.cartDelbtn').on('click', function () {
                        if (confirm('是否要从购物车删除这件商品?')) {
                            fnPdAnimateEnd($(this).parents('.cartListWrap'), $(this).attr('data-pdid'), $(this).attr('data-spid'));
                        }
                    });
                    // 计算订单总额
                    o.countAmount();
                    // 限购数量
                    o.promLimit = parseInt($('.productCountNumi').eq(0).attr('data-prom-limit'));
                    // 红包检查
                    envsCheck();
                });
            }

            // 加载收货地址缓存数据
            localStorageAddrCache();

            // 余额支付点击监听
            $('#cart-balance-check').click(function () {
                if (parseFloat($('#cart-balance-pay').text()) > 0) {
                    // dep
                    $('#order_amount').html('&yen;' + countOrderAmount(this.checked));
                }
            });

        };

        util.getconfig(function (f) {

            o.settings = f;

            if (o.settings.reci_exp_open !== undefined && parseInt(o.settings.reci_open) === 1) {
                // 发票税点
                o.ReciTex = o.settings.reci_perc / 100;
            }

            // 税点是否包括运费
            o.TexIncludeExp = parseInt(o.settings.reci_exp_open) === 1 ? true : false;

            // 加载运费模板
            util.getExpTemplate(function (f1) {
                o.ExpFeeTemplate = f1;
                // 加载购物车数据
                o.loadCartData();
            });
        });


        /**
         * 购物车删除商品动画func
         * @param {type} node 商品section DOM节点
         * @param {type} pdid 删除按钮data-pdid
         * @param {type} spid 删除按钮data-spid
         * @returns {undefined}
         */
        function fnPdAnimateEnd(node, pdid, spid) {

            node.addClass('animated fadeOutUp')//增加animate样式
                    //监听动画结束回调
                    .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                        delFromCart(pdid, spid);
                    });

        }

        // 数量变化监听
        function fnPdCountChangerLis() {

            $('.productCountNumi').on('input', function () {
                Cart.set($(this).attr('data-mhash'), $(this).val());
                o.countAmount();
            });

            // 数量--
            $('.productCountMinus').bind({
                'touchend touchcancel mouseup': function (event) {
                    event.preventDefault();
                    var node = $(this).parent().find('.productCountNumi');
                    if (parseInt(node.val()) <= 1) {
                        if (!o.isProm && confirm('是否要从购物车删除这件商品?')) {
                            fnPdAnimateEnd($(this).parents('.cartListWrap'), $(this).attr('data-pdid'), $(this).attr('data-spid'));
                        }
                    } else {
                        node.val(parseInt(node.val()) === 1 ? 1 : node.val() - 1);
                        if (!o.isProm) {
                            Cart.set(node.attr('data-mhash'), node.val());
                        } else {
                            o.promCount = node.val();
                        }
                    }
                    node = null;
                    o.countAmount();
                    envsCheck();
                }
            });

            // 数量++
            $('.productCountPlus').bind({
                'touchend touchcancel mouseup': function (event) {
                    event.preventDefault();
                    var node = $(this).parent().find('.productCountNumi');
                    if (o.isProm) {
                        if (parseInt(node.val()) < o.promLimit) {
                            node.val(parseInt(node.val()) + 1);
                            o.promCount = node.val();
                        }
                    } else {
                        node.val(parseInt(node.val()) + 1);
                        Cart.set(node.attr('data-mhash'), node.val());
                    }
                    node = null;
                    o.countAmount();
                    envsCheck();
                }
            });
        }

        /**
         * 红包检查
         * @returns {undefined}
         */
        function envsCheck() {
            $('.envsItem').each(function () {
                var envs = $(this);
                envs.addClass('hidden');
                o.envsAva = false;
                $('.pd-envstr').each(function () {
                    if (envs.attr('data-pid').indexOf($(this).attr('data-pid')) !== -1 || envs.attr('data-pid') === '') {
                        var tprice = $(this).parent().find('.dprice').attr('data-price') * $(this).parent().find('.productCountNumi').val();
                        if (envs.attr('data-req') <= tprice) {
                            envs.removeClass('hidden');
                            o.envsAva = true;
                        }
                    }
                });
            });
        }

        /**
         * toggle class
         */
        $('.envsItem i').bind({
            'touchend touchcancel mouseup': function (event) {
                event.preventDefault();
                if ($(this).hasClass('checked')) {
                    $('.envsItem i').removeClass('checked');
                    setEnvs(false, 0, 0);
                } else {
                    $('.envsItem i').removeClass('checked');
                    $(this).toggleClass('checked');
                    // 红包计入
                    var req = $(this).parent().attr('data-req');
                    var dis = $(this).parent().attr('data-dis');
                    var Id = $(this).parent().attr('data-id');
                    setEnvs(Id, req, dis);
                }
                o.countAmount();
            }
        });

        $('.reciItem i').bind({
            'touchend touchcancel mouseup': function (event) {
                event.preventDefault();
                $(this).toggleClass('checked');
                if ($(this).hasClass('checked')) {
                    o.isReci = true;
                    $('#reciWrap').show();
                } else {
                    o.isReci = false;
                    $('#reciWrap').hide();
                }
                o.countAmount();
            }
        });

        /**
         * 设置红包选项
         * @param {type} Id
         * @param {type} Req
         * @param {type} Dis
         * @returns {undefined}
         */
        function setEnvs(Id, Req, Dis) {
            o.envsId = Id;
            o.envsReq = Req || 0;
            o.envsDis = parseFloat(Dis) || 0;
        }

        /**
         * 删除订单商品
         * @param {type} productId
         * @param {type} spid
         * @returns {undefined}
         */
        function delFromCart(productId, spid) {
            Cart.del(productId, spid);
            $('#cartsec' + productId).remove();
            if (countOrderAmount() === 0) {
                fnEmptyCartTip();
                o.countAmount(0);
            } else {
                o.countAmount();
            }
        }

        /**
         * localStorage 地址缓存
         * @returns {undefined}
         */
        function localStorageAddrCache() {
            if (o.Storage && o.Storage.getItem('addr-set') === "1" && o.Storage.getItem('orderAddress')) {
                expressData = JSON.parse(o.Storage.getItem('orderAddress'));
                if (expressData.proviceFirstStageName !== undefined) {
                    // 收货地址加载标记
                    window.addressloaded = true;
                    // 显示收货地址
                    addressShow();
                } else {
                    expressData = {};
                }
            }
        }

        /**
         * 原始数据测试
         * @returns {undefined}
         */
        function loadTestAddrData() {
            var res = {
                proviceFirstStageName: '广东',
                addressCitySecondStageName: '广州市',
                addressCountiesThirdStageName: '天河区',
                addressDetailInfo: '新燕花园三期1201 新燕花园三期1201 新燕花园三期1201 新燕花园三期1201',
                addressPostalCode: 510006,
                telNumber: 18565518404,
                userName: '陈永才'
            };
            res.Address = res.proviceFirstStageName + res.addressCitySecondStageName + res.addressCountiesThirdStageName + res.addressDetailInfo;
            res.err_msg = 'edit_address:ok';
            addAddressCallback(res);
        }

        window.loadTestAddrData = loadTestAddrData;

        /**
         * 获取收货地址回调函数
         * @param {type} res
         * @returns {undefined}
         */
        function addAddressCallback(res) {
            if (res.err_msg === 'edit_address:ok') {
                window.expressData = res;
                expressData.Address = expressData.proviceFirstStageName + expressData.addressCitySecondStageName + expressData.addressCountiesThirdStageName + expressData.addressDetailInfo;
                res.Address = expressData.Address;
                // 缓存到Storage
                o.Storage.setItem('addr-set', '1');
                o.Storage.setItem('orderAddress', JSON.stringify(res));
                // 收货地址加载标记
                window.addressloaded = true;
                addressShow();
                // 地址变动 重新计算订单总额
                o.countAmount();
            } else {
                $('#wrp-btn').html('授权失败');
            }
        }

        function addressShow() {
            $('#wrp-btn').remove();
            $('#express-name').html(expressData.userName);
            $('#express-person-phone').html(expressData.telNumber);
            $('#express-address').html(expressData.Address);
        }

        // 传出全局
        window.addAddressCallback = addAddressCallback;

        /**
         * 计算订单总额
         * @param {Boolean} balan_pay
         * @returns {Number}
         */
        function countOrderAmount(balan_pay) {

            // 余额支付
            balan_pay = balan_pay | false;

            /**
             * @param float ret 订单总金额
             * @param float tweight 订单商品总重量
             * @type Number|Number
             */
            var ret = 0, tweight = 0;

            // var city = expressData.addressCitySecondStageName;
            var prov = expressData.proviceFirstStageName;

            var isEmpExp = false;

            // 固定运费
            var expFixed = 0;

            if ($('.cartListDesc').length > 0) {
                $('.cartListDesc').each(function (lis, node) {
                    // 单价
                    var dprice = parseFloat($('.dprice', node).attr('data-price'));
                    // 数量
                    var dcount = parseInt($('.dcount', node).val());
                    // 固定运费
                    var expFix = parseInt($('.dprice', node).attr('data-expfee'));
                    // 重量
                    var weight = parseInt($('.dprice', node).attr('data-weight'));
                    // 计算商品总价 不包括运费
                    ret += (dprice * dcount);
                    if (expFix > 0) {
                        // 固定运费
                        expFixed += expFix;
                        return;
                    }
                    if (weight === 0) {
                        isEmpExp = true;
                    }
                    tweight += (weight * dcount);
                });
            } else {
                return 0;
            }

            // 联合包邮
            if (isEmpExp) {
                tweight = 0;
            }

            if (balan_pay) {
                ret -= parseFloat($('#cart-balance-pay').text());
                if (ret < 0)
                    ret = 0;
            }

            // 订单实际额 不包括运费、优惠
            o.ActalFee = ret;

            // 红包抵扣
            if (o.envsId !== false) {
                ret -= o.envsDis;
                if (ret < 0) {
                    ret = 0;
                }
                // 红包抵扣提示
                $('#envs_amount').html('-&yen;' + o.envsDis.toFixed(2));
                $('#envsDisTip').show();
            } else {
                $('#envsDisTip').hide();
            }

            // 总运费
            o.ExpFee = countExpFee(tweight, prov) + expFixed;

            o.ExpFee.toFixed(2);

            // 运费
            $('#order_yunfei').html('&yen;' + o.ExpFee.toFixed(2));
            // 总价
            $('#order_amount_sig').html('&yen;' + ret.toFixed(2));

            // 计算发票税
            if (o.isReci) {
                if (o.TexIncludeExp) {
                    o.Tex = (ret + o.ExpFee) * o.ReciTex;
                } else {
                    o.Tex = (ret) * o.ReciTex;
                }
                $('#reciTip_amount').html('&yen;' + o.Tex.toFixed(2));
                $('#reciTip').show();
            } else {
                o.Tex = 0;
                $('#reciTip').hide();
            }

            // 订单总额
            o.TotalFee = ret + o.ExpFee + o.Tex;

            $('#orderSummay, #optinfo, #wechat-payment-btn, #wechat-reqpay-btn').removeClass('hidden').show();

            return (o.TotalFee).toFixed(2);
        }

        /**
         * 计算运费
         * @param {type} tweight
         * @param {type} prov
         * @returns {undefined}
         */
        function countExpFee(tweight, prov) {

            if (tweight === 0) {
                return 0;
            }

            if (prov !== undefined) {
                prov = prov.replace('省', '');
                prov = prov.replace('市', '');
            } else {
                return 0;
            }

            var expTmp = expTmpCheck(prov, o.ExpFeeTemplate);

            if (expTmp === false || expTmp.ffee === undefined) {
                return 0;
            }

            o.ExpFeeInitial = parseFloat(expTmp.ffee);
            // 首重
            if (tweight <= o.settings.exp_weight1) {
                o.ExpFee = o.ExpFeeInitial;
            } else {
                // 续重
                tweight -= o.settings.exp_weight2;
                o.ExpFee = Math.ceil(tweight / 1000);
                o.ExpFee *= parseFloat(expTmp.ffeeadd);
                o.ExpFee += o.ExpFeeInitial;
            }

            return o.ExpFee;
        }

        /**
         * 运费字符串匹配
         * @param {type} prov
         * @param {type} ExpFeeTemplate
         * @returns {Function.ExpFeeTemplate|o._defaults.ExpFeeTemplate|c.ExpFeeTemplate|B.dtd.ExpFeeTemplate|qx.ExpFeeTemplate|q.ExpFeeTemplate|String.ExpFeeTemplate|$@call;param.ExpFeeTemplate}
         * @param {type} prov
         * @param {type} ExpFeeTemplate
         * @returns {qx.ExpFeeTemplate|Function.ExpFeeTemplate|q.ExpFeeTemplate|o._defaults.ExpFeeTemplate|$@call;param.ExpFeeTemplate|c.ExpFeeTemplate|B.dtd.ExpFeeTemplate|String.ExpFeeTemplate}
         */
        function expTmpCheck(prov, ExpFeeTemplate) {
            for (var p in ExpFeeTemplate) {
                var found = false;
                var ExpFs = ExpFeeTemplate[p].province.split('|');
                for (var k in ExpFs) {
                    if (ExpFs[k].indexOf(prov) !== -1 || prov.indexOf(ExpFs[k]) !== -1) {
                        found = true;
                        break;
                    }
                }
                if (found) {
                    return ExpFeeTemplate[p];
                }
            }
            return false;
        }

        // 购物车为空 提示
        function fnEmptyCartTip() {
            $('#order_yunfei').html('&yen;0');
            $('#order_amount_sig').html('&yen;0');
            $('#order_amount_sig').html('&yen;0');
            $('#orderDetailsWrapper').html('<div id="cartnothing" onclick="location=\'' + shoproot + '\'">购物车空空如也，去逛逛吧</div>');
            $('#orderSummay').hide();
            $('#optinfo').hide();
            $('#wechat-payment-btn').hide();
            $('#wechat-reqpay-btn').hide();
            $('.orderopt').hide();
        }

        /**
         * 获取收货地址
         * @returns {undefined}
         */
        function fnSelectAddr() {
            if ($('#addrOn').val() === '1') {
                WeixinJSBridge.invoke('editAddress', addrsignPackage, addAddressCallback);
            } else {
                fnPickAddress();
            }
        }

        util.fnTouchEnd('#express_address', fnSelectAddr);

        /**
         * 非微信接口获取收货地址
         * @todo opt
         * @returns {undefined}
         */
        function fnPickAddress() {
            $('#addrPick').load(shoproot + '?/Uc/selectOrderAddress/body=true', function () {
                $('.addrw').click(address_item_click);
                $('#addr-add-btn1').click(function () {
                    $('#addr-add').show();
                    $('#addr-select').hide();
                });
                $('#addr-add-btn-back').click(function () {
                    $('#addr-add').hide();
                    $('#addr-select').show();
                });
                $('#addr-add-btn').click(address_save);
                $('#addrPick').fadeIn();
            });
        }

        /**
         * 发起微信支付
         * @returns {undefined}
         */
        function wepayCall() {

            if (o.envsAva && !o.envsAsked && !o.envsId) {
                confirm('您有红包可用哦，使用红包可享受优惠');
                o.envsAsked = true;
                return false;
            }

            if (o.Tex > 0 && $('#reci_head').val() === '') {
                alert('请填写发票抬头');
                return false;
            }

            // 判断收货地址是否已经获取
            if (!addressloaded) {
                fnSelectAddr();
                return false;
            }

            if (o.isProm) {
                o.cartData = '{"' + o.promId + '":' + o.promCount + '}';
            } else {
                o.cartData = o.Storage.getItem('cart');
            }

            if ($('#payOn').val() === '1') {
                // 微信支付开通
                if (false === window.addressloaded && typeof WeixinJSBridge !== "undefined") {
                    WeixinJSBridge.invoke('editAddress', addrsignPackage, addAddressCallback);
                    return false;
                }

                if (o.cartData === '{}' || !o.cartData) {
                    return false;
                }

                // cartData cache
                if (o.Storage.getItem('carthash') && CryptoJS.MD5(o.cartData + o.envsId).toString() === o.Storage.getItem('carthash').toString()) {
                    window.orderId = o.Storage.getItem('tmporder');
                } else {
                    o.Storage.removeItem('tmporder');
                    o.Storage.removeItem('carthash');
                    window.orderId = false;
                }

                $('#wechat-payment-btn').addClass('disable').html('支付发起中...');

                expressData.exptime = $('#exptime').val() !== '' ? $('#exptime').val() : '随时';

                // 生成一个订单
                if (false === orderId) {
                    $.post($('#paycallorderurl').val(), {
                        addrData: expressData,
                        cartData: o.cartData,
                        balancePay: $('#cart-balance-check')[0].checked ? 1 : 0,
                        expfee: o.ExpFee,
                        remark: $('#input-remark').val(),
                        envsId: o.envsId,
                        reciHead: $('#reci_head').val(),
                        reciCont: $('.recis:checked').val(),
                        reciTex: o.Tex
                    }, function (r) {
                        if (r.ret_code === 0) {
                            orderGenhandle(r.ret_msg);
                        } else {
                            $('#wechat-payment-btn').removeClass('disable').html('微信安全支付');
                            alert('订单创建失败！');
                        }
                    });
                } else {
                    orderGenhandle(orderId);
                }
            } else {
                // 微信支付未开通，直接下单
                // 生成一个订单
                if (!window.orderCreateLock) {
                    window.orderCreateLock = true;
                    $.post($('#paycallorderurl').val(), {
                        addrData: expressData,
                        cartData: o.cartData,
                        balancePay: $('#cart-balance-check')[0].checked ? 1 : 0,
                        yun: o.ExpFee,
                        leword: $('#f-uname').val() + ' ' + $('#f-upid').val(),
                        uname: $('#f-uname').val(),
                        upid: $('#f-upid').val()
                    }, function (Id) {
                        window.orderCreateLock = false;
                        if (parseInt(Id) > 0) {
                            if (!o.isProm) {
                                Cart.clear();
                            }
                            alert('下单成功！');
                            location.href = shoproot + '?/Uc/orderlist/';
                        } else {
                            alert('订单创建失败！');
                        }
                    });
                }
            }

            // 订单生成回调
            function orderGenhandle(Id) {
                orderId = parseInt(Id) > 0 ? parseInt(Id) : false;
                // 写入订单id缓存Key
                o.Storage.setItem('carthash', CryptoJS.MD5(o.cartData + o.envsId));
                // 写入订单id缓存
                o.Storage.setItem('tmporder', orderId);

                if (o.TotalFee === 0 && o.ActalFee > 0) {
                    // 如果订单总额为0.不需要支付
                    Cart.clear();
                    window.payed = true;
                    window.location.href = shoproot + '?/Uc/home';
                    $('#wechat-payment-btn').removeClass('disable').html('微信安全支付');
                } else {
                    if (orderId > 0 && false === window.payed) {
                        // [HttpPost]
                        $.post(shoproot + "?/Order/ajaxGetBizPackage/", {
                            orderId: orderId
                        }, function (bizPackage) {
                            // 订单映射
                            bizPackage.success = wepayCallback;
                            // 发起微信支付
                            wx.chooseWXPay(bizPackage);
                            // 提示
                            $('#wechat-payment-btn').removeClass('disable').html('微信安全支付');
                        });
                    }
                }

            }
        }

        function reqpayCall() {
            // 判断收货地址是否已经获取
            if (!addressloaded) {
                fnSelectAddr();
                return false;
            }

            if (o.isProm) {
                o.cartData = '{"' + o.promId + '":' + o.promCount + '}';
            } else {
                o.cartData = o.Storage.getItem('cart');
            }

            // 微信支付开通
            if (false === window.addressloaded && typeof WeixinJSBridge !== "undefined") {
                WeixinJSBridge.invoke('editAddress', addrsignPackage, addAddressCallback);
                return false;
            }

            if (o.cartData === '{}' || !o.cartData) {
                return false;
            }

            // cartData cache
            if (o.Storage.getItem('carthash') && CryptoJS.MD5(o.cartData + o.envsId).toString() === o.Storage.getItem('carthash').toString()) {
                window.orderId = o.Storage.getItem('tmporder');
            } else {
                o.Storage.removeItem('tmporder');
                o.Storage.removeItem('carthash');
                window.orderId = false;
            }

            $('#wechat-reqpay-btn').addClass('disable').html('正在生成订单...');

            // 生成一个订单
            if (!window.orderId) {
                $.post($('#paycallorderurl').val(), {
                    addrData: expressData,
                    cartData: o.cartData,
                    balancePay: $('#cart-balance-check')[0].checked ? 1 : 0,
                    yun: o.ExpFee,
                    leword: '',
                    envsId: o.envsId,
                    reciHead: $('#reci_head').val(),
                    reciCont: $('.recis:checked').val(),
                    reciTex: o.Tex,
                    req: 1
                }, orderGenhandleReq);
            } else {
                orderGenhandleReq(orderId);
            }

            /**
             * 请人代付
             * @param {type} Id
             * @returns {undefined}
             */
            function orderGenhandleReq(Id) {
                Cart.clear();
                orderId = parseInt(Id) > 0 ? parseInt(Id) : false;
                // 写入订单id缓存Key
                o.Storage.setItem('carthash', CryptoJS.MD5(o.cartData + o.envsId));
                // 写入订单id缓存
                o.Storage.setItem('tmporder', orderId);
                // 处理代付请求
                if (orderId > 0) {
                    location.href = shoproot + '?/Order/reqPay/id=' + orderId;
                }
            }

        }
        /**
         * 微信支付回调
         * @param {type} res
         * @returns {undefined}
         */
        function wepayCallback(res) {
            Cart.clear();
            window.payed = true;
            window.location.href = shoproot + '?/Order/order_success/?orderid=' + orderId;
            $('#wechat-payment-btn').removeClass('disable').html('微信安全支付');
        }

        $('#wechat-payment-btn').click(wepayCall);
        $('#wechat-reqpay-btn').click(reqpayCall);

        $('#exptime').bind('change', function () {
            $(this).parent().find('.value').html($(this).val());
        });

        $('#input-remark').width($(window).width() - 100);

    });

});