<?php
require_once ('base.php');
$obj = new stdClass ();
$obj->status = 500;

/* 首页头部banner */
$sql_banner = "select id,b_app_img,b_url,b_title from cixi_banner where b_channel<>2 and b_publish_state=2 and b_bygroup=1 order by b_sort desc,id desc limit 0,5";
$banner_info = PdbcTemplate::query ( $sql_banner, null, PDO::FETCH_OBJ );

/* 外部资讯列表 */
$sql_news_one = "select  id,n_title,n_brief,n_list_img,n_source,create_time from cixi_news where n_class=1 and n_channel<>2 and n_publish_state=2 order by create_time desc,id desc limit 0,5 ";
$news_one_info = PdbcTemplate::query ( $sql_news_one, null, PDO::FETCH_OBJ );

/* 内部资讯列表 */
$sql_news_two = "select id,n_title,n_brief,n_list_img,n_source,create_time from cixi_news where n_class=2 and n_channel<>2 and n_publish_state=2 order by create_time desc,id desc limit 0,5";
$news_two_info = PdbcTemplate::query ( $sql_news_two, null, PDO::FETCH_OBJ );

/* 成功案例列表 */
$sql_case = "select id,s_name,img_deal_logo from cixi_deal where is_case=1 and is_publish=2 and is_effect=3 and is_delete=0 order by  sort asc,id desc limit 0,3";
$case_info = PdbcTemplate::query ( $sql_case, null, PDO::FETCH_OBJ );

if (empty ( $banner_info ) && empty ( $news_one_info ) && empty ( $news_two_info ) && empty ( $case_info )) {
	$obj->status = 500;
	$obj->r = "无内容";
	CommonUtil::return_info ( $obj );
	return;
}

$banner_list = array ();
if ($banner_info) {
	foreach ( $banner_info as $k => $v ) {
		$info = new stdClass ();
		$info->id = is_null ( $v->id ) ? "" : $v->id;
		if(strstr($v->b_url,"cisdaq.com")){
			if(strstr($v->b_url,"/news/")){
				// 提取链接里面的数字ID
				if (preg_match ( '/(\d+)/', $v->b_url, $b_url )) {
					$info->b_url =(string)($b_url [1]) ;
				}  
			}
			else{
				$info->b_url =$v->b_url;
			} 
		}
		else 
		{
			$info->b_url = "";
		}	
		$info->b_app_img = is_null ( $v->b_app_img ) ? "" : $v->b_app_img;
		$info->b_title = is_null ( $v->b_title ) ? "" : $v->b_title;
		array_push ( $banner_list, $info );
	}
}
 
$obj->status = 200;
$obj->banner_list = $banner_list;
$obj->news_list = $news_one_info;
$obj->cisdaq_news_list = $news_two_info;
$obj->case_deal = $case_info;

CommonUtil::return_info ( $obj );
?>