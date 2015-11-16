
/* global shoproot, DataTableConfig */

ajaxLock = false;

DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner', 'ueditor', 'jUploader'], function ($, util, fancyBox, dataTables, Spinner, ueditor, jUploader) {

    $(function () {

        var uepLoaded = false;

        $('body').css('overflow-x', 'hidden');

        $('#gs-form-title').unbind('keyup').keyup(function () {
            $('.appmsg_title > a').html($(this).val());
        });
        $('#gs-form-desc').unbind('keyup').keyup(function () {
            $('.appmsg_desc').html($(this).val());
        });

        uep = UM.getEditor('ueditorp', {
            autoHeight: true
        });
        uep.ready(function () {
            uepLoaded = true;
        });

        $.jUploader({
            button: 'thumbUp',
            action: '?/WdminAjax/groupSendImageUpload/',
            onComplete: function (fileName, response) {
                if (response.s > 0) {
                    $('#thumbUp').addClass('ove');
                    util.Alert('图片上传成功');
                    $('#appmsimg-preview').attr('src', shoproot + response.link.substr(1, response.link.length)).show();
                    $('#catimgpath').val(response.img);
                } else {
                    util.Alert('上传图片失败，请联系技术支持');
                }
            }
        });

        $('#thumbUp').hover(function () {
            if (!$(this).hasClass('ove')) {
                $(this).addClass('hover');
            }
        }, function () {
            if (!$(this).hasClass('ove')) {
                $(this).removeClass('hover');
            }
        });

        $('#save_gmess_btn').click(function () {
            if (!ajaxLock) {
                ajaxLock = true;
                var data = $('#uploadForm').serializeArray();
                $.post('?/WdminAjax/alterGmessPage/', {
                    title: data[1].value,
                    content: uep.getContent(),
                    desc: data[2].value,
                    catimg: data[0].value,
                    msgid: $(this).attr('data-id')
                }, function (res) {
                    if (res.status > 0) {
                        util.Alert('保存成功！', false, function () {
                            window.location.href = '?/WdminPage/gmess_list';
                        });
                    } else {
                        ajaxLock = false;
                        util.Alert('保存失败！', true);
                    }
                });
            }
        });

        $('#del_gmess_btn').click(function () {
            if (confirm('要删除这个素材吗？和这个素材相关的文章将失效')) {
                $.post('?/Gmess/ajaxDelByMsgId/', {msgid: $(this).attr('data-id')}, function (r) {
                    if (r.status > 0) {
                        util.Alert('删除成功！', false, function () {
                            location.href = $('#http_referer').val();
                        });
                    } else {
                        util.Alert('删除失败！', true);
                    }
                });
            }
        });

        util.onresize(function () {
            if (uepLoaded) {
                uep.setWidth($('.gs-label:eq(0)').width());
            }
        });
    });

});