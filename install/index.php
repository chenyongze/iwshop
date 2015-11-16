<?php
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
?>
<!DOCTYPE HTML>
<html lang='zh-CN'>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>iWshop Install Guide</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href="wshop_install.css" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" charset="utf-8" src="../static/script/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="../static/script/validation/dist/jquery.validate.js"></script>
        <script type="text/javascript" charset="utf-8" src="../static/script/validation/dist/lang-cn.js"></script>
        <script type="text/javascript" charset="utf-8" src="wshop_install.js"></script>
    </head>
    <body>
        <div id="center">
            <div id="logo"></div>
            <div id="install-main">
<!--                <form id="sept0">

                    <div class="clearfix">
                        <div class="left" style="width: 240px;">
                            <div class="head">目录读写权限</div>
                            <ul>
                                <li>/static/Thumbnail/</li>
                                <li>/tmp/Thumbnail/</li>
                                <li>/uploads/banner/</li>
                                <li>/uploads/banner_tmp/</li>
                                <li>/uploads/brands/</li>
                                <li>/uploads/gmess/</li>
                                <li>/uploads/gmess_tmp/</li>
                                <li>/uploads/product_hpic/</li>
                                <li>/uploads/product_hpic_tmp/</li>
                                <li>/uploads/product_qrcode/</li>
                                <li>/uploads/ueditor/</li>
                                <li>/html/gmess/</li>
                                <li>/html/products/</li>
                            </ul>
                        </div>
                        <div class="left" style="width: 200px;">
                            <div class="head">扩展检测</div>
                            <ul>
                                <li>curl</li>
                                <li>mcrypt</li>
                                <li>pdo_mysql</li>
                                <li>mysql</li>
                                <li>gd2</li>
                            </ul>
                        </div>
                        <div class="left">
                            <div class="head">其他检查</div>
                            <ul>
                                <li>magic_quotes_gpc = Off</li>
                            </ul>
                        </div>
                    </div>
                    
                                        目录读写权限
                    
                                        php版本
                    
                                        扩展检测
                    
                                        magic_quotes_gpc


                </form>-->
                <form id="sept1">
                    <div class="field">
                        <div class="gs-label">微店名称 <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-shopname" class="required" tabindex="1" type="text"/><div class="gs-tip">你自己微店的名称：比如“我的微店”</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">数据库地址 <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-dbaddress" class="required" tabindex="2" type="text" value="127.0.0.1"/><div class="gs-tip">数据库的地址：本地数据库是localhost，如果是localhost，推荐使用127.0.0.1性能较佳。</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">数据库名 <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-dbname" class="required" tabindex="3" type="text" value="iWshop"/><div class="gs-tip">数据库名，不存在则自动创建，需要数据库用户具有创建数据库权限。</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">数据库用户名 <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-dbusername" class="required" tabindex="4" type="text" value="root"/><div class="gs-tip">数据库用户名：默认为root</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">数据库密码 <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-dbpassword" class="required" tabindex="5" type="text" value=""/><div class="gs-tip">数据库密码</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">后台管理员账号 <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-adminname" class="required" tabindex="6" type="text"/><div class="gs-tip"></div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">后台管理员密码 <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-adminpassword" class="required" tabindex="7" type="text"/><div class="gs-tip"></div></div>
                    </div>
                </form>
                <form id="sept2">
                    <div class="field">
                        <div class="gs-label">APPID <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-appid" class="required" tabindex="1" type="text"/><div class="gs-tip">微信公众号APPID</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">APPSECRET <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-appsecret" class="required" tabindex="2" type="text"/><div class="gs-tip">微信公众号APPSECRET</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">TOKEN <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-token" class="required" tabindex="3" type="text"/><div class="gs-tip">微信公众号验证TOKEN</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">商户ID <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-partner" class="required" tabindex="4" type="text"/><div class="gs-tip">微信支付商户ID，在微信支付开通邮件里面可以查看</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">通加密串(partnerKey) <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-partnerkey" class="required" tabindex="6" type="text"/><div class="gs-tip">商户通加密串(partnerKey)，在微信商户平台中API秘钥设置中设置</div></div>
                    </div>
                    <div class="field">
                        <div class="gs-label">系统根目录 <div class="gs-tip1"></div></div>
                        <div class="gs-text"><input name="f-docroot" id="docroot" class="required" tabindex="6" type="text"/><div class="gs-tip"></div></div>
                    </div>
                    <input type="hidden" value="" id="f-domain" name="f-domain" />
                </form>
                <div id="sept3">

                </div>
                <div style="text-align: center">
                    <!--<a class="button green" id="install-btn0" href="javascript:;">正在检测</a>-->
                    <a class="button green" id="install-btn3" href="javascript:;">上一步</a>
                    <a class="button green" id="install-btn1" href="javascript:;">下一步</a>
                    <a class="button green" id="install-btn2" href="javascript:;">马上安装</a>
                </div>
            </div>
        </div>
    </body>
</html>