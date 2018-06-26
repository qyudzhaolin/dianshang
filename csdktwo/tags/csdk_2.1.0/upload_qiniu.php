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
    $data['error'] = getFileError($_FILES[$file_name]['error']);
    $data['file'] = $_FILES[$file_name];
    ajax_return($data);
}

$size = $_FILES[$file_name]['size'] / (1024 * 1024);
if ($max_size != 0 && $size > $max_size) {
    $data['status'] = 4;
    $data['error'] = '请确保文件在' . $max_size . 'M内';
    ajax_return($data);
}

/*
 * @log type属性（程序可自动判断），接口返回文件类型type (sunerju 2016-07-06)
 */
if ($_FILES[$file_name]['type'] == 'image/gif' || $_FILES[$file_name]['type'] == 'image/jpeg' || $_FILES[$file_name]['type'] == 'image/pjpeg' || $_FILES[$file_name]['type'] == 'image/png' || $_FILES[$file_name]['type'] == 'image/x-png') {
    
    if($file_type && $file_type != 'img'){
        $data['status'] = 30;
        $data['error'] = getFileTypeError($file_type);
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
        $data['error'] = '请确保图片宽度为' . $x . '图片高度为' . $y . '';
        ajax_return($data);
    }
    if ($y != 0 && $y != $height) {
        $data['status'] = 6;
        $data['error'] = '请确保图片宽度为' . $x . '图片高度为' . $y . '';
        ajax_return($data);
    }
    
    $image = uploadQiniu($_FILES[$file_name]['tmp_name'], "csdkimg");
    if ($image['key']) {
        // 缓存到页面
        $data['status'] = 1;
        $data['key'] = $image['key'];
        $data['url'] = getQiniuPathJs($image['key'], "img");
        $data['scale'] = round( $height/ $width, 2); // 宽高比例
        $data['type'] = 1; // 图片类型
        ajax_return($data);
    } else {
        $data['status'] = 2;
        $data['error'] = '文件上传失败';
        ajax_return($data);
    }
} else if ($_FILES[$file_name]['type'] == 'application/pdf' || $_FILES[$file_name]['type'] == 'application/vnd.adobe.pdx' || $_FILES[$file_name]['type'] == 'application/kswps') {    $image = uploadQiniu($_FILES[$file_name]['tmp_name'], "csdkbp");
    
    if($file_type && $file_type != 'pdf'){
        $data['status'] = 31;
        $data['error'] = getFileTypeError($file_type);
        ajax_return($data);
    }

    if ($image['key']) {
        // 缓存到页面
        $data['status'] = 1;
        $data['key'] = $image['key'];
        $data['url'] = APP_ROOT . "bp_viewer/get_bp.php?key=" . $image['key'];
        $data['type'] = 2; // pdf类型
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

function getFileError($code){
    switch($code) {
        case 1:
            // 文件大小超出了服务器的空间大小
            $error = "The file is too large (server).";
            break;

        case 2:
            // 要上传的文件大小超出浏览器限制
            $error = "The file is too large (form).";
            break;
             
        case 3:
            // 文件仅部分被上传
            $error = "The file was only partially uploaded.";
            break;
             
        case 4:
            // 没有找到要上传的文件
            $error = "No file was uploaded.";
            break;
             
        case 5:
            // 服务器临时文件夹丢失
            $error = "The servers temporary folder is missing.";
            break;
             
        case 6:
            // 文件写入到临时文件夹出错
            $error = "Failed to write to the temporary folder.";
            break;
    }

    return $error;
}

function getFileTypeError($type){
    switch($type){
        case 'img':
            $error = "只允许上传gif、jpg、png格式";
            break;
        case 'pdf':
            $error = "只允许上传pdf格式";
            break;
        default:
            $error = "文件格式错误";
    }
    
    return $error;
}
?>