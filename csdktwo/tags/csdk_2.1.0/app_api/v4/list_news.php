<?php
require_once('base.php');
require_once('/fun/session.php');


define("PAGE_SIZE", 10);

// $user_status = CommonUtil::verify_user();
// CommonUtil::check_status($user_status);
$obj = new stdClass;
$obj->status = 500;

$page_num 	= isset($_POST['page'])?trim($_POST['page']):1;
$n_class 	= isset($_POST['news_type'])?trim($_POST['news_type']):1;
$deal_id	= isset($_POST['deal_id'])? trim($_POST['deal_id']):NULL;
$is_deal_cate_news	= isset($_POST['is_deal_cate_news'])? trim($_POST['is_deal_cate_news']):NULL;
 

$page_offset= PAGE_SIZE*$page_num - PAGE_SIZE;
$page_rows 	= PAGE_SIZE;

// $uid		= trim($_POST["uid"]) ;
// $sign_sn   	= trim($_POST["sign_sn"]) ;
	   
	   $sql_news="select id,n_title,n_brief,n_list_img,n_source,create_time  from cixi_news where   n_channel<>2 and  n_publish_state=2  ";

	   if(!is_null($deal_id)){
	   		if($is_deal_cate_news==2){
	   			/**********获取项目所属行业******************/
	   			$sql_cate_deal = " select cate_id from cixi_deal_select_cate where deal_id=?  ";
	   			$para_cate_deal = array( $deal_id);
	   			$r_cate_deal = PdbcTemplate::query($sql_cate_deal,$para_cate_deal);
	   			$cate_deal_list=  "";
	   			if(!empty($r_cate_deal))
	   			{
	   				foreach ($r_cate_deal as $key => $val)
	   				{
	   					$cate_deal_list= $cate_deal_list.' FIND_IN_SET("'.$val->cate_id.'",n_cate) or';
	   				}
	   			}
	   			$cate_deal_list=(substr($cate_deal_list,0,strlen($cate_deal_list)-2));
	   			$condition = " and ($cate_deal_list) and id not in(select id from cixi_news where n_deal = $deal_id and n_publish_state=2 and n_channel in (1,3))" ;
	   		}
	   		else{
	   			$condition = " and  	n_deal 	= $deal_id " ;}
	   }
	   else
	   {        $condition = "and n_class={$n_class} " ;}
	   	
	   	$sql_page	="
				order by create_time desc,id desc limit {$page_offset}, {$page_rows}";
	   $sql_final = $sql_news." ".$condition." ".$sql_page;
	   
	   $news_info=  PdbcTemplate::query($sql_final,null,PDO::FETCH_OBJ);
	   
       if(empty($news_info))
        {
        		$obj ->status = 500;
			    $obj ->r = "无消息";
			    CommonUtil::return_info($obj);
	   	 	    return;
        }
 
		 $obj->status = 200;
	  	 $obj->data = $news_info;
	  	 CommonUtil::return_info($obj);
?>