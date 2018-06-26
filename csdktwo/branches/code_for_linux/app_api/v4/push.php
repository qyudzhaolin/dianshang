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
 require_once ('base.php');
// require_once ('/Cache/Lite.php');
// $user_status = CommonUtil::verify_user ();
// CommonUtil::check_status ( $user_status );

$id = isset ( $_GET ['id'] ) ? trim ( $_GET ['id'] ) : NULL;
if (is_null ( $id )) {
	$obj->r = "推送消息ID为空";
	CommonUtil::return_info ( $obj );
	return;
}
$sql_push = "
				select * from	cixi_message	push
		where 	  push.id = ?		";
$para_push = array ($id);
$r_push = PdbcTemplate::query ( $sql_push, $para_push, PDO::FETCH_OBJ, 1);
if(!empty($r_push)){

?>

</head>

<body class="box_model">
	<section class="content">
		<h1 class="title"><?php echo($r_push->title);?></h1>
		<div class="report_msg">
			<span class="author"> </span>
			<span class="date"><?php echo(date("Y年m月d日 H:i",$r_push->create_time));?></span>
			 
		</div>
		 
		<div class="font15 margin"> 
		 <?php echo($r_push->content);?>
		</div>
		
	</section>
<p class="copyright"> 

</p>
 
</body>
<?php } 
else {
	echo("<body class='box_model'><section class='content'><h1 class='title'>您请求的内容无法被找到</h1></section></body>");
}
?>

</html>
