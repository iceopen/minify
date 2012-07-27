<?php
header("Content-Type:text/html;charset=utf-8");
/* 压缩 */
$MINIFY = true;
/* 默认cdn地址 */
$YOUR_CDN = 'http://cdn.iceinto.com/';

require 'jsmin.php';
require 'cssmin.php';

/**
 * 根据地址获取文件内容
 * @param $url
 * @return mixed|string
 */
function get_contents($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $str = curl_exec($ch);
    curl_close($ch);
    if ($str !== false) {
        return $str;
    } else {
        return '';
    }
}

/**
 * 得到扩展名
 * @param $file_name
 * @return mixed
 */
function get_extend($file_name)
{
    $extend = explode(".", $file_name);
    $va = count($extend) - 1;
    return $extend[$va];
}

$files = array();
//只处理js 和 css
$header = array(
    'js' => 'Content-Type: application/x-javascript',
    'css' => 'Content-Type: text/css',
);
//文件类型
$type = '';
foreach ($_GET as $k => $v) {
    $k = str_replace(array('_'), array('.'), $k);
    if (empty($type)) {
        $type = get_extend($k);
    }
    //文件不存在
    $inner_str = file_get_contents($YOUR_CDN . $k);
    //文本的处理
    if (preg_match('/js|css/', $type) && $inner_str) {
        if ($MINIFY == true && $type == 'js') {
            $files[] = JSMin::minify($inner_str);
        } else if ($MINIFY == true && $type == 'css') {
            $files[] = cssmin::minify($inner_str);
        } else {
            $files[] = $inner_str;
        }
    }
}
header("Expires: " . date("D, j M Y H:i:s", strtotime("now + 10 years")) . " GMT");
if (!empty($type)) {
    header($header[$type]); //文件类型
    if (preg_match('/js|css/', $type)) {
        $result = implode("", $files);
    } else {
        //非文本的处理
        $result = implode("", $files[0]);
    }
    echo $result;
}
?>
