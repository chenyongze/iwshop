/*
 * Copyright (C) 2014 koodo@qq.com.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

// jquery validation 
// @see http://www.w3cschool.cc/jquery/jquery-plugin-validate.html

$(function () {
    $('form').eq(0).show();
    $('input').eq(0).focus();
    var vali = {
        errorPlacement: function (error, element) {
            element.parent().parent().find('.gs-tip1').eq(0).html(error);
        }};
    var docroot = location.pathname.substr(0, location.pathname.lastIndexOf('/') + 1).replace('install/', '');
    // 第一步
    $('#install-btn1').click(function () {
        if ($('#sept1').validate(vali).form()) {
            var f1 = $('#sept1').serializeObject();
            vali_db(f1, function (val) {
                if (val) {
                    $('#sept1').hide();
                    $('#sept2').show();
                    $('#install-btn1').hide();
                    $('#install-btn2').css('display', 'inline-block');
                    $('#install-btn3').css('display', 'inline-block');
                    $('#sept2 input').eq(0).focus();
                } else {
                    alert('数据库连接失败');
                }
            });
        }
    });
    $('#docroot').val(docroot);
    $('#f-domain').val(location.origin + '/');
    // 第二步
    $('#install-btn2').click(function () {
        if ($('#sept2').validate(vali).form()) {
            var f1 = $('#sept1').serializeObject();
            var f2 = $('#sept2').serializeObject();
            var f = extend({}, [f1, f2]);
            delete f[0];
            delete f[1];
            // 导入数据库
            install_dbtable(f, function (val, r) {
                if (val) {
                    alert('数据库导入成功！');
                    // 写入config文件
                    install_config(f, function (val, r) {
                        if (val) {
                            location.href = docroot + '?/Wdmin/login/';
                        }
                    });
                } else {
                    alert(r);
                }
            });

        }
    });
    $('#install-btn3').click(function () {
        $('#sept2').hide();
        $('#sept1').show();
        $(this).hide();
        $('#install-btn1').css('display', 'inline-block');
        $('#install-btn2').hide();
    });
});

function extend(des, src, override) {
    if (src instanceof Array) {
        for (var i = 0, len = src.length; i < len; i++)
            extend(des, src[i], override);
    }
    for (var i in src) {
        if (override || !(i in des)) {
            des[i] = src[i];
        }
    }
    return des;
}

jQuery.prototype.serializeObject = function () {
    var obj = new Object();
    $.each(this.serializeArray(), function (index, param) {
        if (!(param.name in obj)) {
            obj[param.name] = param.value;
        }
    });
    return obj;
};

// 检查数据库连接
function vali_db(param, _func) {
    param.a = 'db_valid';
    $.post('ajax_installer.php', param, function (r) {
        _func(r === "1");
    });
}

// 进行数据库导入
function install_dbtable(param, _func) {
    param.a = 'db_install';
    $.post('ajax_installer.php', param, function (r) {
        _func(r === "1", r);
    });
}

function install_config(param, _func) {
    param.a = 'config_install';
    $.post('ajax_installer.php', param, function (r) {
        _func(r === "1", r);
    });
}