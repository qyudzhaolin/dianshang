<?php
require './system/common.php';
// 上传项目图片到七牛
$file_name = $_REQUEST['file_name']; // var_dump($_FILES[$file_name]);
$file_type = $_REQUEST['file_type'];
$x = isset($_REQUEST['x']) ? $_REQUEST['x'] : 0;
$y = isset($_REQUEST['y']) ? $_REQUEST['y'] : 0;
$max_size = isset($_REQUEST['max_size']) ? $_REQUEST['max_size'] : 0;
if ($_FILES[$file_name]['error'] != 0) {
    $data['status'] = 5;
    $data['error'] = '请确保图片在2M内';
    $data['file'] = $_FILES[$file_name];
    ajax_return($data);
}
$imageinfo = getimagesize($_FILES[$file_name]['tmp_name']);
$xy = explode(" ", $imageinfo[3]);

$width = explode("=", $xy[0]);
$len = strlen($width[1]);
$width = intval(substr($width[1], 1, - 1));
$height = explode("=", $xy[1]);
$len = strlen($height[1]);
$height = intval(substr($height[1], 1, - 1));

if ($x != 0 && $x != $width) {
    $data['status'] = 5;
    $data['error'] = '请确保图片宽度为' . $x;
    ajax_return($data);
}
if ($y != 0 && $y != $height) {
    $data['status'] = 6;
    $data['error'] = '请确保图片宽度为' . $x . '图片高度为' . $y . '';
    ajax_return($data);
}
$size = $_FILES[$file_name]['size'] / (1024 * 1024);
if ($max_size != 0 && $size > $max_size) {
    $data['status'] = 4;
    $data['error'] = '请确保图片在' . $max_size . 'M内';
    ajax_return($data);
}

if ($_REQUEST['file_type'] != 'bp') {
    if ($_FILES[$file_name]['type'] == 'image/gif' || $_FILES[$file_name]['type'] == 'image/jpeg' || $_FILES[$file_name]['type'] == 'image/pjpeg' || $_FILES[$file_name]['type'] == 'image/png' || $_FILES[$file_name]['type'] == 'image/x-png') {
        $image = uploadQiniu($_FILES[$file_name]['tmp_name'], "csdkimg");
        if ($image['key']) {
            // 缓存到页面
            $data['status'] = 1;
            $data['key'] = $image['key'];
            $data['url'] = getQiniuPathJs($image['key'], "img");
            $data['scale'] = round( $height/ $width, 2); // 宽高比例
            ajax_return($data);
        } else {
            $data['status'] = 2;
            $data['error'] = '文件上传失败';
            ajax_return($data);
        }
    } else {
        $data['status'] = 3;
        $data['error'] = '只允许上传gif、jpg、png格式';
        ajax_return($data);
    }
} else {
    // if($_FILES[$file_name]['type']=='application/msword' ||$_FILES[$file_name]['type']=='application/pdf' ||$_FILES[$file_name]['type']=='text/plain'||$_FILES[$file_name]['type']=='application/mspowerpoint'||$_FILES[$file_name]['type']=='application/application/vnd.ms-powerpoint'||$_FILES[$file_name]['type']=='application/vnd.adobe.pdx'){
    if ($_FILES[$file_name]['type'] == 'application/pdf' || $_FILES[$file_name]['type'] == 'application/vnd.adobe.pdx' || $_FILES[$file_name]['type'] == 'application/kswps') {
        $image = uploadQiniu($_FILES[$file_name]['tmp_name'], "csdkbp");
        if ($image['key']) {
            // 缓存到页面
            $data['status'] = 1;
            $data['key'] = $image['key'];
            $data['scale'] = round( $height/ $width, 2); // 宽高比例
            $data['url'] = APP_ROOT . "bp_viewer/get_bp.php?key=" . $image['key'];
            ajax_return($data);
        } else {
            $data['status'] = 2;
            $data['error'] = '文件上传失败';
            ajax_return($data);
        }
    } else {
        $data['status'] = 3;
        $data['error'] = '只允许上传pdf格式';
        // $data['error'] ='只允许上传pdf、txt、word、ppt格式';
        ajax_return($data);
    }
}

?>