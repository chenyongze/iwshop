/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
requirejs(['config'], function(config) {
    
    require(['jquery', 'util', 'Slider'], function($, util, Slider) {
        
        Slider.slide('#slider');

        $(window).bind('resize', function() {
            $('.nav-round').each(function() {
                $(this).height($(this).width());
            });
            $('.sliderTip').each(function() {
                $(this).css('left', ($(this).parent().width() - this.clientWidth) / 2);
            });
            $('.hplist').each(function(i, node) {
                $(node).find('img').each(function() {
                    $(this).height($(this).width());
                });
            });
        }).resize();

        util.searchListen();

    });
});