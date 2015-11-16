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

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner', 'jUploader', 'ztree', 'ztree_loader', 'datetimepicker'], function ($, util, fancyBox, dataTables, Spinner, jUploader, ztree, treeLoader) {

    var relId = $('#pids').val();

    $.datetimepicker.setLocale('zh');

    $('#dt1').datetimepicker({
        format: 'Y-m-d H:i:s'
    });
    $('#dt2').datetimepicker({
        format: 'Y-m-d H:i:s'
    });

    // 3> 选择产品
    fnFancyBox('#sProduct', function () {
        
        $('.fancybox-skin').css('background', '#fff');
        var inlist = $('#pds-pdright #inlists');

        // 初始化目录树
        treeLoader.init('#pds-catLeft', '?/vProduct/ajaxGetCategroys/r=' + (new Date()).getTime());

        // 目录树点击回调函数
        treeLoader.setting.callback.onClick = function (event, treeId, treeNode) {
            inlist.html('');
            Spinner.spin(inlist.get(0));
            $.get('?/FancyPage/ajaxPdBlocks/id=' + treeNode.dataId, function (html) {
                inlist.html(html);
                $('.pdBlock').bind('click', pdBlockLis);
                $('#okSProduct').bind('click', okSProduct);
            });
        };

        $('#pdSelectSearch').bind('keydown', function (e) {
            var key = e.which;
            if (key === 13) {
                if ($(this).val() === '') {
                    return false;
                }
                // [HttpGet]
                $.get('?/FancyPage/ajaxPdBlocks/key=' + $(this).val(), function (html) {
                    inlist.html(html);
                    $('.pdBlock').bind('click', pdBlockLis);
                    $('#okSProduct').bind('click', okSProduct);
                });
            }
        });
    });

    /**
     * 商品块 点击监听
     * @returns {undefined}
     */
    function pdBlockLis() {
        $(this).toggleClass('selected');
        $(this).find('.sel').toggleClass('hov');
    }

    /**
     * 商品选择Fancybox回调
     * @returns {undefined}
     */
    function okSProduct() {
        var blocks = $('.pdBlock.selected').clone();
        blocks.removeClass('selected').find('.sel').remove();
        $('#ProductItem').prepend(blocks);
        pdBlockAdjust();
        $.fancybox.close();
    }

    /**
     * 商品选择自适应调整
     * @returns {undefined}
     */
    function pdBlockAdjust() {
        // 删除监听
        var allBlocks = $('#ProductItem .pdBlock');
        var Relid = [];
        allBlocks.hover(function () {
            var i = $('<i class="pd-image-delete"> </i>');
            i.bind('click', function () {
                $(this).parent().fadeOut(function () {
                    $(this).remove();
                    pdBlockAdjust();
                });
            });
            $(this).append(i);
        }, function () {
            $(this).find('.pd-image-delete').remove();
        });
        // 计算relId
        allBlocks.each(function (i, node) {
            $(this).find('.sel').remove();
            Relid.push($(this).attr('data-id'));
        });
        // 赋值
        relId = Relid.join(',');
        // 选择计数
        $('#spdCount').removeClass('hidden').html('已选择' + $('#ProductItem .pdBlock').length + '个产品');
        // 隐藏提示
        $('#spdTip').hide();
    }

    pdBlockAdjust();

    // 图片上传
    $.jUploader({
        button: 'upload_banner',
        action: '?/WdminAjax/BannerImageUpload/',
        onComplete: function (fileName, response) {
            if (response.s > 0) {
                $('#banner').val(response.img);
                $('#loading').height(0);
                $('#catimage').attr('src', shoproot + response.link.substr(1, response.link.length)).fadeIn(function () {
                    Spinner.stop();
                });
                $('#cat_none_pic').hide();
            } else {
                alert('上传图片失败，请联系技术支持');
            }
        },
        onUpload: function () {
            $('#catimage').attr('src', '').hide();
            $('#loading').height(100);
            Spinner.spin($('#loading').get(0));
        }
    });

    $('#saveBtn').click(function () {
        var banner = $('#banner').val();
        var id = $(this).attr('data-id');
        $.post('?/wSettings/alterSection/', {
            banner: banner,
            name: $('#name').val(),
            pid: relId,
            id: id,
            relId: $('#pd-cat-select').val(),
            ftime: $('#dt1').val(),
            ttime: $('#dt2').val(),
            bsort: +$('#bsort').val()
        }, function (r) {
            if (r > 0) {
                if (id > 0) {
                    $('#saveBtn').attr('data-id', r);
                    util.Alert('保存成功');
                } else {
                    util.Alert('添加成功');
                    window.setTimeout(function () {
                        location.href = '?/WdminPage/settings_section';
                    }, 2000);
                }
            } else {
                util.Alert('操作失败', true);
            }
        });
    });

});