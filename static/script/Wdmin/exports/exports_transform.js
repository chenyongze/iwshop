
var dT;
var loadingLock = false;

DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner', 'jUploader'], function($, util, fancyBox, dataTables, Spinner, jUploader) {
    $(function() {
        window.util = util;

        // 上传提示
        fnFancyBox('#_trig1,#_trig2', function() {

        });

        if ($('#islocal').val() === '0') {
            $('#_trig1').click();
        } else {
            $('.gmess-sending').show();
            Spinner.spin($('.gmess-sending').get(0));
            $.get(shoproot + '?/WdminAjax/ajaxOrderByIdsExporting/ids=' + $('#ids').val(), function(html) {
                Spinner.stop();
                $('.gmess-sending').hide();
                $('#od-exp-frame').html(html);
                $('.dTable').dataTable(DataTableConfig).api();
                $('#data-exp-trans').removeClass('hidden');
                $('.pricSig').on('keyup', function() {
                    if ($(this).val() === '') {
                        $(this).val(0);
                    }
                    $('#pricTotal' + $(this).attr('rel')).val($(this).val() * $(this).attr('data-count'));
                });
            });
        }

        $.jUploader({
            button: $('#data-exp-upload').get(0),
            action: shoproot + '?/XlsxExport/ajaxXlsTransform/',
            accept: 'application/vnd.ms-excel',
            onUpload: function(fileName) {
                $('#data-exp-upload').hide();
                Spinner.spin($('#uploadDiv').get(0));
            },
            onComplete: function(fileName, response, html) {
                Spinner.stop();
                $('#od-exp-frame').html(html);
                $('#data-exp-trans').removeClass('hidden');
                $('.dTable').dataTable(DataTableConfig).api();
                $('.search-left-box').removeClass('hidden');
                $.fancybox.close();
                util.Alert('上传成功');
                // 单价自动计算
                $('.pricSig').on('keyup', function() {
                    if ($(this).val() === '') {
                        $(this).val(0);
                    }
                    $('#pricTotal' + $(this).attr('rel')).val($(this).val() * $(this).attr('data-count'));
                });
            }
        });

        // 目标仓库全体切换
        $('#exp-type-all').on('change', function() {
            var value = $(this).val();
            $(".exp-type").each(function() {
                $(this).find("option[value='" + value + "']")[0].selected = true;
            });
        });

        $('#data-exp-trans').unbind('click').click(function() {
            if (!loadingLock) {
                loadingLock = true;
                var node = $(this);
                var data = {};

                $('#exp-type-all option').each(function() {
                    data["_" + $(this).val()] = [];
                });

                node.html('处理中...');
                $('.gmess-sending').show();
                Spinner.spin($('.gmess-sending').get(0));

                $('.dTable tbody tr').each(function() {
                    var tr = $(this);
                    var _data = {};
                    var _exptype = "_" + tr.find('.exp-type').val();
                    tr.find('input').each(function() {
                        _data[$(this).attr('name')] = $(this).val().replace("'", "");
                    });
                    data[_exptype].push(_data);
                });

                $.post(shoproot + '?/XlsxExport/exportOrderListWithType/', {
                    data: data
                }, function(json) {
                    var seted = false;
                    $('#downloadDiv').find('.wd-btn').remove();
                    // <a class="wd-btn primary hidden" id="data-exp-dl" target='_blank' href=''>点击下载</a>
                    for (var i in json) {
                        if (json[i].set === 1) {
                            seted = true;
                            $('#downloadDiv').append('<a class="wd-btn primary" style="padding: 0 20px;display:block;margin-top:5px;text-align:left;" id="data-exp-dl" target=\'_blank\' href="' + json[i].link + '">点击下载 <' + json[i].name + '></a>');
                        }
                    }
                    if (seted) {
                        util.Alert('数据生成成功');
                        $('#_trig2').click();
                    } else {
                        util.Alert('数据生成失败，请联系技术支持', true);
                    }
                    Spinner.stop();
                    node.html('导出数据');
                    $('.gmess-sending').hide();
                    loadingLock = false;
                });
            }
        });
    });
});