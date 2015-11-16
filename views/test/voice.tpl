<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>{$title} - {$settings.shopname}</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="format-detection" content="telephone=no" />
        <link href="{$docroot}static/css/wshop_cart.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="{$docroot}static/script/lib/crypto-md5.js"></script>
    </head>
    <body>

        <div style="margin-top: 100px;">
            <div id="tip"></div>

            <button id="v1">录音</button>

            <button id="v2">停止</button>

            <button id="v3">播放</button>

            <button id="v4">上传</button>

            <button id="v5">下载</button>

        </div>

        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

        <script type="text/javascript">
            wx.config({
                debug: true,
                appId: '{$signPackage.appId}',
                timestamp: {$signPackage.timestamp},
                nonceStr: '{$signPackage.nonceStr}',
                signature: '{$signPackage.signature}',
                jsApiList: ['startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice']
            });
        </script>

        <script src="{$docroot}static/script/jquery-1.7.2.min.js"></script>
        <script type="text/javascript">
            $(function () {
                var o = window.localStorage;
                var localId;
                var serverId;
                if (o) {
                    localId = o.getItem('localvoiceId');
                    $('#tip').html("本地记录" + localId);
                }
                $('#v1').click(function () {
                    wx.startRecord();
                    $('#tip').html('开始录音');
                });
                $('#v2').click(function () {
                    wx.stopRecord({
                        success: fnRecordEnd
                    });
                });
                $('#v3').click(function () {
                    wx.playVoice({
                        localId: localId
                    });
                });
                $('#v4').click(function () {
                    wx.uploadVoice({
                        localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
                        isShowProgressTips: 1, // 默认为1，显示进度提示
                        success: function (res) {
                            serverId = res.serverId; // 返回音频的服务器端ID
                            $('#tip').html("上传成功" + serverId);
                        }
                    });
                });
                // 下载数据库mediaId对应的语音数据
                $('#v5').click(function () {
                    wx.downloadVoice({
                        serverId: serverId, // 需要下载的音频的服务器端ID，由uploadVoice接口获得
                        isShowProgressTips: 1, // 默认为1，显示进度提示
                        success: function (res) {
                            var localId = res.localId; // 返回音频的本地ID
                        }
                    });
                });
                function fnRecordEnd(res) {
                    localId = res.localId;
                    o.setItem('localvoiceId', localId);
                    $('#tip').html("录音完毕" + localId);
                }
            });
        </script>

        {include file="../global/copyright.tpl"}

    </body>
</html>
