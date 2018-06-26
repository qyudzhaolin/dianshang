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
	<link rel="stylesheet" type="text/css" href="../h5/css/resetH5.css" />
	<link rel="stylesheet" type="text/css" href="../h5/css/style.css" />
<?php
 require_once ('/base.php');
// require_once ('/Cache/Lite.php');
// $user_status = CommonUtil::verify_user ();
// CommonUtil::check_status ( $user_status );

$id = isset ( $_GET ['id'] ) ? trim ( $_GET ['id'] ) : NULL;
if (is_null ( $id )) {
	$obj->r = "新闻ID为空";
	CommonUtil::return_info ( $obj );
	return;
}
$sql_news = "
				select * from	cixi_news	news
		where 	news.n_publish_state=2 and news.id = ?		";
$para_news = array ($id);
$r_news = PdbcTemplate::query ( $sql_news, $para_news, PDO::FETCH_OBJ, 1);
if(!empty($r_news)){

?>

</head>

<body class="box_model">
	<section class="content">
		<h1 class="title"><?php echo($r_news->n_title);?></h1>
		<div class="report_msg">
			<span class="author"><?php echo($r_news->n_source);?></span>
			<span class="date"><?php echo(date("Y年m月d日 H:i",$r_news->create_time));?></span>
			 
		</div>
		<img src="<?php $appnews_img=(trim(IMG_DOMAIN.($r_news->n_app_img)));
		$appnews_img=$appnews_img."?imageView2/1/";echo($appnews_img);?>" style="width: 100%;"alt="">
		<div class="font15 margin"> 
		 <?php echo($r_news->n_desc);?>
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
