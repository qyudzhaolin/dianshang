<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="apple-touch-fullscreen" content="yes" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta name="format-detection" content="telephone=no"/>
	<meta http-equiv="Cache-Control" content="no-cache"/>
	<link rel="stylesheet" type="text/css" href="/style/css/resetH5.css" />
	<link rel="stylesheet" type="text/css" href="/style/css/style.css" />
<?php
/**
 * H5新闻详情----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */
define('RUN_ENV',isset($_SERVER['RUN_ENV'])?$_SERVER['RUN_ENV']:'develop');
require_once('../conf/'.RUN_ENV.'/conf_db.php');
require_once('../function/Api_common.php'); 
define('IMG_DOMAIN', "http://img.cisdaq.com/") ; 
// 服务层新闻详情
$id = isset ( $_GET ['id'] ) ? trim ( $_GET ['id'] ) : NULL;
if (is_null ( $id )) {
	$obj->r = "新闻ID为空";
	CommonUtil::return_info ( $obj );
	return;
}
$param = array(
		'id'  => $id  
);
$result=request_service_api('Contents.News.queryNewsDetailByCondition',$param);
$resultstatus=$result['status'];
$r_news=$result['response'] ;
if ($resultstatus==0&&!empty($r_news)) {
?>
</head>
<body class="box_model">
	<section class="content">
		<h1 class="title"><?php echo($r_news[0]['n_title']);?></h1>
		<div class="report_msg">
			<span class="author"><?php echo($r_news[0]['n_source']);?></span>
			<span class="date"><?php echo(date("Y年m月d日 H:i",$r_news[0]['create_time']));?></span>
		</div>
		<img src="<?php $appnews_img=(trim(IMG_DOMAIN.($r_news[0]['n_app_img'])));
		$appnews_img=$appnews_img."?imageView2/1/";echo($appnews_img);?>" style="width: 100%;"alt="">
		<div class="font15 margin"> 
		 <?php echo($r_news[0]['n_desc']);?>
		</div>
		
	</section>
<p class="copyright">（版权所有，未经授权不得转载）
</p>
</body>
<?php } 
else {
	echo("<body class='box_model'><section class='content'><h1 class='title'>您请求的内容无法被找到</h1></section></body>");
}
?>
</html>
