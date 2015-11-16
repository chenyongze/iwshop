
var shoproot = location.pathname.substr(0, location.pathname.lastIndexOf('/') + 1);

//Spinner 配置项
var Spinner = new Spinner({
    lines: 11, // The number of lines to draw
    length: 7, // The length of each line
    width: 2, // The line thickness
    radius: 9, // The radius of the inner circle
    corners: 0.9, // Corner roundness (0..1)
    rotate: 0, // The rotation offset
    direction: 1, // 1: clockwise, -1: counterclockwise
    color: '#44b549', // #rgb or #rrggbb or array of colors
    speed: 1.2, // Rounds per second
    trail: 25, // Afterglow percentage
    shadow: false, // Whether to render a shadow
    hwaccel: true, // Whether to use hardware acceleration
    className: 'spinner', // The CSS class to assign to the spinner
    zIndex: 2e9, // The z-index (defaults to 2000000000)
    top: 'auto', // Top position relative to parent
    left: 'auto' // Left position relative to parent
});

var shoproot = location.pathname;

// 默认card Index
var defaultCard = 0;

var tHeight = false;

/**
 * 高度计算标准值
 */
var stdHeight = document.documentElement.clientHeight;

/**
 * 当前iframe导航页面名
 * @type String|nav
 */
var currentSubnav = '';

/**
 * 当前iframe页面名
 * @type Boolean|page
 */
var currentURI = false;

// jq lis
$(function() {

    $('.navItem').on('click', function() {
        var n = $(this);
        $('#' + n.attr('rel')).find('a').eq(0).click();
    });

    $('.cap-nav-item').on('click', function() {

        var nThis = $(this);

        var nP = $('#' + nThis.parent().attr('id').replace('subnav', 'navitem'));

        var page = nThis.attr('data-page');

        if (!nThis.parent().hasClass('up')) {
            $('.subnavs.up').slideUp(200).removeClass('up');
            nThis.parent().addClass('up');
            nThis.parent().slideDown(200, load);
        } else {
            load();
        }

        function load() {
            $('.navItem.hover').removeClass('hover');
            nP.addClass('hover');

            $('.subnavs a.hov').removeClass('hov');
            nThis.addClass('hov');
            // 加载页面
            $('#iframe_loading').height(stdHeight).show();
            Spinner.spin($('#iframe_loading')[0]);
            // 跳转页面
            currentURI = '?/WdminPage/' + page;
            $('#right_iframe').get(0).contentWindow.location.replace(currentURI);
        }

    });

    // 是否在首页，某些情况需要区分登陆页和首页
    if ($('.wdmin-main').length > 0) {
        $('.navItem:eq(' + defaultCard + ')').addClass('hover').click();
        // 问题反馈按钮监听
        fnFancyBox('#navitem14', wentFankui);
        // 在首页 自动确认订单
        $.get('?/Order/confirmExpress/rec=1');
    }

    // 第一级iframeonload清除loading动画
    $('#right_iframe').on('load', function() {
        window.setTimeout(function() {
            Spinner.stop();
            $('#iframe_loading').fadeOut();
        }, 500);
    });

    // resize
    window.onresize = __resize__;
    __resize__();
});

/**
 * window resizing lis
 * @returns {undefined}
 */
function __resize__() {
    tHeight = !tHeight ? $('#topnav').height() + 1 : tHeight;
    if (document.documentElement.clientWidth > 1366) {
        stdHeight = document.documentElement.clientHeight - 20;
    } else {
        stdHeight = document.documentElement.clientHeight;
    }
    $('.wdmin-main,#rightWrapper').css('height', stdHeight - tHeight + 'px');
    $('#leftNav').css('height', stdHeight - tHeight + 'px');
    // 右侧iframe高度
    $('#right_iframe').css('height', stdHeight - tHeight + 'px');
}

/**
 * 刷新iframe页面
 * @returns {undefined}
 */
function reloadPage() {
    $('#iframe_loading').show();
    $('#right_iframe').get(0).contentWindow.location.replace(currentURI + ($('#right_iframe').attr('src').indexOf("?") !== -1 ? "/?" : "&") + (new Date()).getTime());
}

/**
 * 登陆验证函数
 * @returns {undefined}
 */
function loginCheck() {
    if ($('#pd-form-username').val() !== '' && $('#pd-form-password').val() !== '') {
        if (!loading) {
            loading = true;
            $('#loading').show();
            Spinner.spin($('#loading').get(0));
            $('.login-gbtn').html('正在登录...');
            $.post(shoproot + '?/Wdmin/checkLogin/', {
                admin_acc: $('#pd-form-username').val(),
                admin_pwd: $('#pd-form-password').val()
            }, function(res) {
                $('#loading').hide();
                Spinner.stop();
                loading = false;
                if (parseInt(res.status) === 1) {
                    Alert('登录成功，正在跳转...');
                    $('.login-gbtn').html('登录成功');
                    location.href = shoproot + '?/Wdmin/';
                } else {
                    $('.login-gbtn').html('登录');
                    Alert('登录失败，用户名或者密码错误！', true);
                }
            });
        }
    }
}

/**
 * 问题反馈
 * @returns {undefined}
 */
function wentFankui() {
    $('#save_ques_btn').unbind('click').click(function() {
        if ($('#ques-title').val() === '') {
            Alert('请输入标题', true);
        }
        $.post(shoproot + '?/sysQuesion/ajaxSubmitQuestion/', {
            title: $('#ques-title').val(),
            content: $('#ques-desc').val()
        }, function(r) {
            if (parseInt(r) > 0) {
                Alert('提交成功，我们会尽快跟进');
                $.fancybox.close();
            } else {
                Alert('提交失败', true);
            }
        });
    });
}

/**
 * 顶部alert提示
 * @param {type} message
 * @param {type} warn
 * @param {type} callback
 * @returns {undefined}
 */
function Alert(message, warn, callback) {
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
    window.setTimeout(function() {
        node.slideUp(300, function() {
            if (typeof callback === 'function') {
                callback();
            }
            $('#__alert__').remove();
        });
    }, 3000);
}