<?php
global $db_host ;
global $db_name;
global $db_username;
global $db_password;

$db_host = "127.0.0.1" ;
$db_port="3306";
$db_name = "csdk" ;
$db_username = "root" ;
$db_password = "root" ;

// 供服务调用方配置使用
define('SERVICE_API_HOST','http://service.cisdaq.com/') ;     //服务Api主机地址
define('SERVICE_API_HOST_ENTRY','index.php') ;           //服务Api主机入口文件地址
define('SERVICE_API_DEFAULT_MODULE','Wap') ;              //服务Api默认Api模块名称,用于没有指定Api模块的时候进行默认值指定
define('SERVICE_API_REQUEST_SECKEY','HTTP://WWW.CISDAQ.COM/_2016@CSDK_#@DU^^&JGK_((*&gjGH') ;        //服务Api请求安全密钥

// PDF文件，新闻，图标H5拼接网址，换个环境请替换域名
define('PDF_DOMAIN', "http://dev.cisdaq.com/") ;  
?>
