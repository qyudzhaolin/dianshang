<?php
require_once('../system/common.php');
$key	= isset($_REQUEST['key'])? trim($_REQUEST['key']):'Fl8jvaD40eiPWHmPt_UjwbTSysCk';
$type	= isset($_REQUEST['type'])? trim($_REQUEST['type']):'';

//获取页数
$url=getQiniuPath_key($key);
$url=$url."?odconv/jpg/info";
$page_num_url=getQiniuPath_android($url);
$contents = file_get_contents($page_num_url); 
$page=json_decode($contents);
$page_num=$page->page_num;

if($type=='app'){
	for ($page_now = 1; $page_now <= $page_num; $page_now++) 
	{
		$url=getQiniuPath_key($key);
		$url1=$url."?odconv/jpg/page/".$page_now."/density/150/quality/50/resize/800";
		$img_url=getQiniuPath_android($url1);
		echo "<img src='".$img_url."' id='bp_url' style='width:100%'/>";
	}
}else{
	for ($page_now = 1; $page_now <= $page_num; $page_now++) 
	{
		$url=getQiniuPath_key($key);
		$url1=$url."?odconv/jpg/page/".$page_now."/density/150/quality/80/resize/1366";
		$img_url=getQiniuPath_android($url1);
		echo "<img src='".$img_url."' id='bp_url' style='width:100%'/>";
	}
}

return;



/*require_once('../system/common.php');
$key	= isset($_REQUEST['key'])? trim($_REQUEST['key']):'Fl8jvaD40eiPWHmPt_UjwbTSysCk';
$page_now	= isset($_REQUEST['page'])? trim($_REQUEST['page']):1;
$src	= isset($_REQUEST['src'])? trim($_REQUEST['src']):"";
$type	= isset($_REQUEST['type'])? trim($_REQUEST['type']):1;


if ($src=='check') {
	$url=getQiniuPath_key($key);
	$url=$url."?odconv/jpg/page/".$page_now."/density/150/quality/80/resize/800";
	echo getQiniuPath_android($url);
	return;
}



//获取页数
$url=getQiniuPath_key($key);
$url=$url."?odconv/jpg/info";
$page_num_url=getQiniuPath_android($url);
$contents = file_get_contents($page_num_url); 
$page=json_decode($contents);
$page_num=$page->page_num;



$url=getQiniuPath_key($key);
$url=$url."?odconv/jpg/page/".$page_now."/density/150/quality/80/resize/800";
$img_url=getQiniuPath_android($url);


echo "
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='UTF-8'>
	<meta content='width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no' name='viewport' id='viewport' />
	<title></title>
</head>
<body>
	<div class='container'>
		<span class='left' onclick='bp_ajax_prev();'><</span>
		<span class='right'onclick='bp_ajax();'>></span>
		<img src='".$img_url."' id='bp_url' style='width:100%;'  />
		<input type='hidden' value='".$key."' id='key_value'/>
		<input type='hidden' value='".$page_now."' id='page_now_value'/>
		<input type='hidden' value='".$page_num."' id='page_num_value'/>
		<span class='show'><span id='show_page'>".$page_now."</span>/".$page_num."</span>
	</div>
</body>
</html>
	<script type='text/javascript' src='js/bp_viewer.js'/></script>
	<style type='text/css'>
	body{font:12px '微软雅黑';}
	.container{position:relative;}
	.left,.right{font-size:6em;position:fixed;left:10px;top:50%;margin-top:-64px;z-index:9999;cursor:pointer;color:rgba(0,0,0,.3);}
	.container .right{position:fixed;left:auto;right:10px;top:50%;}
	.show{width:100px;height:30px;background:rgba(0,0,0,.3);text-align:center;line-height:30px;color:#fff;border-radius:15px;position:absolute;bottom:20px;left:50%;margin-left:-50px;z-index:999999;}
    </style>";

*/
?>


