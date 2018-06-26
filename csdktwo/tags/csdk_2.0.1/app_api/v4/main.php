<?php
require_once('base.php');

define("PAGE_SIZE", 5);

/*验证登录*/
 
// $user_status = CommonUtil::verify_user();
// CommonUtil::check_status($user_status);
$obj = new stdClass;
$obj->status = 500;

// $uid		= trim($_POST["uid"]) ;
// $sign_sn   	= trim($_POST["sign_sn"]) ;

			/*首页头部banner*/
 
			$sql_banner="select id,b_url,b_app_img from cixi_banner where b_channel<>2 and b_publish_state=2 and b_bygroup=1 order by b_sort desc,id desc limit 0,5";
			$banner_info=  PdbcTemplate::query($sql_banner,null,PDO::FETCH_OBJ);

			$sql_news_one="select  id,n_title,n_brief,n_list_img,n_source,create_time from cixi_news where n_class=1 and n_channel<>2 and n_publish_state=2 order by create_time desc,id desc limit 0,5 ";
			//var_dump($sql_news_one);
			$news_one_info=  PdbcTemplate::query($sql_news_one,null,PDO::FETCH_OBJ);

			$sql_news_two="select id,n_title,n_brief,n_list_img,n_source,create_time from cixi_news where n_class=2 and n_channel<>2 and n_publish_state=2 order by create_time desc,id desc limit 0,5";
			//var_dump($sql_news_one);
			$news_two_info=  PdbcTemplate::query($sql_news_two,null,PDO::FETCH_OBJ);
			
			$sql_case="select id,s_name,img_deal_logo from cixi_deal where is_case=1 and is_effect=3 and is_delete=0 order by  sort asc,id desc limit 0,3";
			$case_info=  PdbcTemplate::query($sql_case,null,PDO::FETCH_OBJ);

			if(empty($banner_info)&&empty($news_one_info)&&empty($news_two_info)&&empty($case_info))
				{
				$obj ->status = 500;
				$obj ->r = "无内容";
				CommonUtil::return_info($obj);
				return;
				}
			//$obj_banner = new stdClass;
			//$obj_banner->banner_info=array();
			if ($banner_info) {
			foreach($banner_info as $k=>$v){
				$info=new stdClass;
				//$style_info->id=$v->id;
				$info->id= is_null($v->id) ? "" : $v->id;
 				$info->b_url= is_null($v->b_url) ? "" : $v->b_url;
				$info->b_app_img= is_null($v->b_app_img) ? "" : $v->b_app_img;
				//array_push($obj_banner->banner_info, $info) ;
				}
			}
 
			//$obj_one_info = new stdClass;
			//$obj_one_info->news_one_info=array();
			if ($news_one_info) {
			foreach($news_one_info as $k=>$v){
				$info=new stdClass;
				//$style_info->id=$v->id;
				$info->id= is_null($v->id) ? "" : $v->id;
				$info->n_title= is_null($v->n_title) ? "" : trim($v->n_title);
				$info->n_brief= is_null($v->n_brief) ? "" : $v->n_brief;
				$info->n_list_img= is_null($v->n_list_img) ? "" : $v->n_list_img;
				$info->n_source= is_null($v->n_source) ? "" : $v->n_source;
				$info->create_time= is_null($v->create_time) ? "" : $v->create_time;
				//array_push($obj_one_info->news_one_info, $info) ;
				}
			}

			if ($news_two_info) {
			foreach($news_two_info as $k=>$v){
				$info=new stdClass;
				//$style_info->id=$v->id;
				$info->id= is_null($v->id) ? "" : $v->id;
				$info->n_title= is_null($v->n_title) ? "" : trim($v->n_title);
				$info->n_brief= is_null($v->n_brief) ? "" : $v->n_brief;
				$info->n_list_img= is_null($v->n_list_img) ? "" : $v->n_list_img;
				$info->n_source= is_null($v->n_source) ? "" : $v->n_source;
				$info->create_time= is_null($v->create_time) ? "" : $v->create_time;
				//array_push($obj_one_info->news_one_info, $info) ;
				}
			}
			
			if ($case_info) {
				foreach($case_info as $k=>$v){
					$info=new stdClass;
					//$style_info->id=$v->id;
					$info->deal_id= is_null($v->id) ? "" : $v->id;
					$info->deal_s_name= is_null($v->s_name) ? "" : trim($v->s_name);
					$info->img_deal_logo= is_null($v->img_deal_logo) ? "" : $v->img_deal_logo;
				}
			}
			$obj->status = 200;
			$obj->banner_list = $banner_info;
			$obj->news_list = $news_one_info;
			$obj->cisdaq_news_list = $news_two_info;
			$obj->case_deal = $case_info;
			 
   			CommonUtil::return_info($obj);	
?>