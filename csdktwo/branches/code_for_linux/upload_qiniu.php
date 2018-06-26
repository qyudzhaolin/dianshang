<?php
require './system/common.php';

// 返回加密后的数据
$secKey     = app_conf('SERVICE_API_REQUEST_SECKEY');   #加密密钥
$apiPath    = "Common.QiniuUpload.putFile";            #请求路径

$file_name  = isset($_REQUEST['file_name']) ? trim($_REQUEST['file_name']) : "";
$file_type  = isset($_REQUEST['file_type']) ? trim($_REQUEST['file_type']) : "";
$x          = isset($_REQUEST['x']) ? (int)$_REQUEST['x'] : 0;
$y          = isset($_REQUEST['y']) ? (int)$_REQUEST['y'] : 0;
$max_size   = isset($_REQUEST['max_size']) ? (int)$_REQUEST['max_size'] : 0;
$scale      = isset($_REQUEST['scale']) ? (int)$_REQUEST['scale'] : 0;

if(empty($file_name)){
    $data['status']     = 0;
    $data['message']    = "上传参数缺失";
    ajax_return($data);
}

// JSON参数
$apiArgs = json_encode(array(
    "file_name" => $file_name,
    "file_type" => $file_type,
    "x"         => $x,
    "y"         => $y,
    "max_size"  => $max_size,
    "scale"     => $scale,
    "domain"    => APP_DOMAIN, #跨域问题，需传domain
));

// 加密参数
$signKey = md5($apiArgs.$secKey.$apiPath);

// 返回数据组装
$data = array(
    'status'    => 1,
    'func'      => $apiPath,
    'params'    => $apiArgs,
    'signKey'   => $signKey,
    'apiUrl'    => app_conf('SERVICE_API_HOST').app_conf('SERVICE_API_HOST_ENTRY'),
);

ajax_return($data);
?>