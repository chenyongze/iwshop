/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

DataTableConfig.order = [[0, 'desc']];
requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function ($, util, fancyBox, dataTables) {
    $(function () {
        util.loadCompanyStatNums();

        var dt = $('.dTable').dataTable(DataTableConfig).api();

        fnFancyBox('.com-ajaxqrcode');

        fnFancyBox('.alter-company', function () {
            $('#al-com-save').unbind('click').click(function () {
                var cid = parseInt($(this).attr('data-id'));
                if (cid > 0) {
                    var data = $('#form_alter_company').serializeArray();
                    $.post(shoproot + '?/Company/ajaxAlterCompanyInfo/', {
                        id: cid,
                        data: data
                    }, function (res) {
                        if (res > 0) {
                            $.fancybox.close();
                            location.reload();
                            util.Alert('修改成功');
                        } else {
                            util.Alert('修改失败', true);
                        }
                    });
                }
            });
        });

        // todo
        fnFancyBox('#add_company', function () {
            $('#al-com-save').unbind('click').click(function () {
                var data = $('#form_alter_company').serializeArray();
                $.post(shoproot + '?/Company/ajaxAlterCompanyInfo/', {
                    id: 0,
                    data: data
                }, function (res) {
                    if (res > 0) {
                        util.Alert('添加成功');
                        $.fancybox.close();
                        location.reload();
                    } else {
                        util.Alert('添加失败，请检查你的输入!', true);
                    }
                });
            });
        });

        $('.com-delete-btn').click(function () {
            if (confirm('你确认要删除这个代理么，该操作无法恢复')) {
                var node = $(this);
                $.post(shoproot + '?/Company/AjaxDeleteCompany/', {
                    id: $(this).attr('data-id')
                }, function (res) {
                    if (res > 0) {
                        util.loadCompanyStatNums();
                        util.Alert('删除成功');
                        dt.row(node.parents('tr')).remove().draw();
                    } else {
                        util.Alert('操作失败!', true);
                    }
                });
            }
        });
    });
});