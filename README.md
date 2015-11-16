#iWshop<微信开源商城>

iWshop是一个开源的微信商城。为了保证轻量级，使用了作者自主开发的mvc框架。 

LightMvc现已分离 <a href="https://git.oschina.net/koodo/LightMvc">https://git.oschina.net/koodo/LightMvc</a> 

>iWshop 交流群：470442221

>iWshop 类文档：<a href="http://docs.ycchen.cc/iwshop/package-Wshop.html">http://docs.ycchen.cc/iwshop/package-Wshop.html</a> 

>iWshop 安装教程：<a href="http://git.oschina.net/koodo/iWshop/blob/dev/html/docs/install.md">http://git.oschina.net/koodo/iWshop/blob/dev/html/docs/install.md</a>

>JerryJee提供的教程：<a target="_blank" href="http://www.jiloc.com/2661.html">http://www.jiloc.com/2661.html</a>

##iwshop 配置说明

####一、根目录修改说明


如果是服务器根目录，必须修改为 “/”


如果是服务器子目录，必须修改为”/subroot/” 等有左右斜杠的格式


修改文件：

/config/config.php ($config->shoproot = “目录”)

####二、手动部署说明（暂时）

- 服务器配置过程略。

- 创建/install/install.lock

- 导入/install/iWshop.sql （你懂的）

- 建立默认Admin账户，账户：admin 密码：admin

`INSERT INTO admin VALUES ('1', 'admin', '4a0894d6e8f3b5c6ee0c519bcb98b6b7fd0affcb343ace3a093f29da4b2535604b61f0aebd60c0f0e49cc53adba3fffb', '0', '2015-11-06 10:27:34', '121.33.35.52', 'stat,orde,prod,gmes,user,comp,sett');`

- 后台地址（以域名www.iwshop.cn为例）为：http://www.iwshop.cn/?/Wdmin/login/

- 微信消息接口地址：http://www.iwshop.cn/wechat/              (切莫忘了最后的 / )

####三、目录权限说明

/static/Thumbnail/ 0777

/lib/ClassLoader.php 0777

/tmp/ 0777

/uploads/ 0777

/models/SqlCached.php 0777

请确保您的php配置中magic_quotes_gpc为Off，否则一些功能将失效

####四、配置文件config.php说明

初始配置文件为/config/config_sample.php

请编辑config_sample.php文件并且重命名为config.php

####五、运行环境要求
 
**MySQL 5.5.3+ (utf8mb4编码用于保存带有emoji表情的微信用户昵称)**

PHP5.4+

PHP扩展：php_mysql php_curl php_pdo_mysql php_mcrypt php_gd2


####六、演示项目

<img src="http://down.ycchen.cc/iwshop_release/images/qrcode1.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" /> 
<img src="http://down.ycchen.cc/iwshop_release/images/qrcode2.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" /> 
<img src="http://down.ycchen.cc/iwshop_release/images/qrcode3.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" />
<img src="http://down.ycchen.cc/iwshop_release/images/qrcode4.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" />
<img src="http://down.ycchen.cc/iwshop_release/images/qrcode5.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" />

## 相关下载

><a href="http://download-iwshop.oss-cn-shenzhen.aliyuncs.com/xampp-win32-5.6.12-0-VC11-installer.zip" target="_blank">xampp-win32-5.6.12-0-VC11(Xampp集成安装包Windows)</a> 