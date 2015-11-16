/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
require(['config'], function(config) {

    require(['util', 'Spinner'], function(util, Spinner) {

        var lastPush = '';

        var iFrame = util.q('#mainFrame');

        var loading = util.q('#loading');

        // 页面导航函数，html5 Pushstate
        window.fnRedirect = function(link, state) {
            // state 为 false 时即后退
            state = state === undefined ? true : state;
            if (link === shoproot) {
                link = '?/Index/home/';
            }
            // 使用location replace禁止iframe使用历史记录，否则后退会先后退iframe
            iFrame.setAttribute('src', link);
            //iFrame.contentWindow.location.replace(link);
            if (state) {
                if (link === '?/Index/home/') {
                    link = shoproot;
                }
                // 避免反复push
                if (lastPush !== link) {
                    history.pushState({
                        title: "",
                        url: link
                    }, "", link);
                    lastPush = link;
                }
            } else {
                util.bottomNavSwitch(link);
            }
        };

        // 监听浏览器后退事件
        window.addEventListener('popstate', function(e) {
            fnRedirect(e.state.url, false);
        });

        // 底部导航按钮点击
        util.fnTouchEndRedirect('.bottom_nav > div', function(link) {
            fnRedirect(link);
            loading.style.display = 'block';
            Spinner.spin(loading);
        });

        Spinner.spin(loading);

        // 内iframe加载完毕notify外壳关闭loading
        window.loadNotify = function() {
            var link = iFrame.contentWindow.location.search;
            Spinner.stop();
            loading.style.display = 'none';
            // 同步外壳title
            util.q('title').innerHTML = iFrame.contentWindow.document.title;
            // 同步外壳url
            if (link === '?/Index/home/') {
                link = shoproot;
            }
            // 避免反复push
            if (lastPush !== link) {
                history.pushState({
                    title: "",
                    url: link
                }, "", link);
                lastPush = link;
            }
        };

        // 跳转默认页面
        fnRedirect(iFrame.getAttribute('init-src'));

        util.onresize(function() {
            iFrame.style.height = (document.documentElement.clientHeight - util.q('.bottom_nav').clientHeight) + 'px';
        });
    });
});