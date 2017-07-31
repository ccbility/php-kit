<?php
error_reporting(0);

echo '当前时间戳: ', time(), '<br />';
if ($_POST['timestamp']) {
    echo '时间戳转换成日期 ', date('Y-m-d H:i:s', $_POST['timestamp']);
}
if ($_POST['md5']) {
    echo 'MD5值 ', md5($_POST['md5']);
}
if ($_POST['url']) {
    echo 'url解码 ', urldecode($_POST['url']);
}
if ($_POST['json_str']) {
    echo 'json解码 ', var_dump(json_decode($_POST['json_str'], true));
}
if ($_POST['curl']) {
    $tmp = curlBase($_POST['curl']);
    file_put_contents('tttt.png', $tmp);
}

function curlBase($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch,CURLOPT_PROXY,'127.0.0.1:8888');
    // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36'); 
    // curl_setopt($ch, CURLOPT_REFERER, 'http://www.baidu.com/'); 

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<br>
<br>
<form action="" method="post">
    <input type="text" name='timestamp'>
    <input type="submit" value="时间戳转换成日期">
</form>
<form action="" method="post">
    <input type="text" name='md5'>
    <input type="submit" value="计算出MD5值">
</form>
<form action="" method="post">
    <input type="text" name='url'>
    <input type="submit" value="url解码">
</form>
<form action="" method="post">
    <input type="text" name='json_str'>
    <input type="submit" value="json解码">
</form>
<form action="" method="post">
    <input type="text" name='curl'>
    <input type="submit" value="curl请求">
</form>
<a href="http://tool.oschina.net/regex/">正则式检验</a>
<a href="http://www.bejson.com">json格式化</a>
</body>
</html>
