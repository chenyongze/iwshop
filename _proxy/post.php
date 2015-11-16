<?php
header('Content-Type:application/json');
//if(isset($_POST['mod']) && $_POST['mod'] == 1) $_POST['postdata'] = urldecode(json_encode($_POST['postdata']));
//print_r(json_encode(array('errcode' => 0)));
//
//exit(0);

function decodeUnicode($str) {
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function(
                    '$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
            ), $str);
}

$PostData = $_POST['postdata'];
$PostData['news']['articles'][0]['title'] = urldecode($PostData['news']['articles'][0]['title']);
$PostData['news']['articles'][0]['description'] = urldecode($PostData['news']['articles'][0]['description']);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $_POST['url']);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97'); // 模拟用户使用的浏览器 
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转 
curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer 
curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回 
// post数据
curl_setopt($curl, CURLOPT_POST, 1);
// post的变量
curl_setopt($curl, CURLOPT_POSTFIELDS, str_replace('\/', '/', decodeUnicode(json_encode($PostData))));
$output = curl_exec($curl);
echo $output;
curl_close($curl);
