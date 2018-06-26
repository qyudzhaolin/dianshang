<?php
require_once('base.php');
define("PAGE_SIZE", 5);

/*verify user status for both investor and initiator*/
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$obj = new stdClass;
$obj->status = 500;

$page_num 	= isset($_POST['page'])?trim($_POST['page']):1;
$page_offset= PAGE_SIZE*$page_num - PAGE_SIZE;
$page_rows 	= PAGE_SIZE;

$uid		= trim($_POST["uid"]) ;
$sign_sn   	= trim($_POST["sign_sn"]) ;

/*Filter parameters used in Filter list, there are three dimension filter which are cate, district, period*/
$cate 		= isset($_POST['cate'])? trim($_POST['cate']):NULL;
$district 	= isset($_POST['district'])? trim($_POST['district']):NULL;
$period_id 	= isset($_POST['period_id'])? trim($_POST['period_id']):NULL;
$focus_filter 	= isset($_POST['focus_filter'])? trim($_POST['focus_filter']):NULL;
$update_time 	= isset($_POST['update_time'])? trim($_POST['update_time']):NULL;
/*Parameters for investation intend List, if it is equal to 1, which means the user is going to investation intend List page*/
$intend 	= isset($_POST['intend'])? trim($_POST['intend']):NULL;
$search 	= isset($_POST['search'])? trim($_POST['search']):NULL;
/*****Initializing Variables Which Used In Following Sql Statement *****/
	$sql_select	= "";
	$sql_from	= "";
	$sql_where 	= "";
	$condition 	= "";
	$sql_final 	= "";
	$para_final = array();
$sql_select = "
				select cate_choose 		deal_cates
				      ,id 		   		deal_id
					  ,img_deal_logo 	image_deal_logo
					  ,name				deal_name
					  ,deal_sign		deal_sign
					  ,focus_count		focus_count
					  ,period_id 		period_id
					  ,province			province
					  ,city				city
				";	

$sql_from = "from cixi_deal";

/****** Check if the user is reviewed or not ********************/
$sql_check_user = "
					select is_review from cixi_user 
					where user_type = 1
					and   id = ?
					";
$para_check_user = array($uid);
$result_check_user = PdbcTemplate::query($sql_check_user, $para_check_user, PDO::FETCH_OBJ,0);

/******* Based on reviewed status of investor, they should see different project ********/
/********reviewed investor can see all project***/
if("1"==$result_check_user[0]->is_review)
{
	$sql_where = "
				where vis in (1, 2) 
				";	
}else
{
/********not reviewed investor only can see some project in visibility 2***/
	$sql_where = "
				where vis = 2
					";
}
/*********Based on business requirement, we only display investing and invested project info*****************/
$sql_condition = "and is_effect in (3,4)";
/******** Based on the order of project list, we provide investing project first, 
          then invested project info*********************/
		  
$sql_order_by_condition ="
							order by is_effect desc
							";

/*********Now we are going to make filter functionality**********************/
/****Deal with Categories which are choosed by invetor********/
if(!is_null($cate))
{
		$cate_array 			= explode('_',$cate);
		$cates_condition 		= '';
		foreach($cate_array as $item_in_array)
		{		
			$cates_condition 	= $cates_condition.$item_in_array.',';	
		}
		$cate_condition=substr($cates_condition,0,-1);
		$subQuery_get_deal_id_list = "select deal_id from cixi_deal_select_cate where cate_id in ($cate_condition)";
		$condition .= "and deal.id in ($subQuery_get_deal_id_list)";/******筛选列表条件（方向）*********/
}
if(!is_null($district))
{
	/*********Deal with district***********************/
	$pos = strpos($district, "_");
	if ($pos===false) {
		$condition .= "and 	deal.province  = ?  ";		/******筛选列表条件（地区分类,省市）*********/
		$para_final[]= $district;
	}else{
		$district_array = explode('_',$district);
		$condition .= "and 	deal.province  = ? 	
				   and 	deal.city 	   = ? ";		/******筛选列表条件（地区分类,省市）*********/
		$para_final[]= $district_array[0];
		$para_final[]= $district_array[1];
	}			
}
if(!is_null($period_id))
{
	$period_id_array 	= explode('_',$period_id);
	$period_id_condition 		= '';
	foreach($period_id_array as $item_in_array)
	{		
		$period_id_condition 	= $period_id_condition.$item_in_array.',';	
	}
	$period_id_condition=substr($period_id_condition,0,-1);

	$condition .= "and 	deal.period_id in ($period_id_condition) "; 		
	/******筛选列表条件（阶段分类）*********/
	//$para_final[]= $period_id;
}
/**********Deal with order based on focus_count***********************/

if(!is_null($focus_filter))
{
	$sql_order_by_condition.=",focus_count";
}
if(!is_null($update_time))
{
	$sql_order_by_condition.=",update_time";
}
if(!is_null($search))
{
	$condition .= "and name like '%{$search}%'";
}
 
$sql_order_by_condition.="
							limit 		$page_offset, $page_rows
						"; 
$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_by_condition;
 //var_dump($sql_final);

/******************Execute Sql statement **********************************/

$result = PdbcTemplate::query($sql_final,$para_final,PDO::FETCH_OBJ,0);
$deal_info = array();

		if(!empty($result))
		{
			foreach($result as $key => $val) {
				$obj_final = new stdClass;
				$obj_final->deal_id			= is_null($val->deal_id) ? ""		: $val->deal_id;
				$obj_final->image_deal_logo	= is_null($val->image_deal_logo) ? "" : $val->image_deal_logo;
				$obj_final->deal_name 				= is_null($val->deal_name) ? "" 				: $val->deal_name;
				//$obj_final->view_count 		= is_null($val->view_count) ? 0 		: $val->view_count;
				$obj_final->focus_count 	= is_null($val->focus_count) ? 0	: $val->focus_count;
				$obj_final->deal_cates		= is_null($val->deal_cates) ? ""     	: $val ->deal_cates;
				$obj_final->deal_sign				= is_null($val->deal_sign) ? ""     		: $val ->deal_sign;
				$obj_final->period_id				= is_null($val->period_id) ? 0		: $val ->period_id;
				$obj_final->province				= is_null($val->province) ? 0	: $val ->province;
				$obj_final->city				= is_null($val->city) ? 0   		: $val ->city;
				
				$sql_focus = "select 1 from cixi_deal_focus_log where uid = ? and deal_id = ?";
				$para_focus = array();
				$para_focus[] = $uid;
				$para_focus[] = $val->deal_id;
				$result_focus = PdbcTemplate::query($sql_focus, $para_focus, PDO::FETCH_OBJ, 0);
				if(!empty($result_focus))
				{
					$obj_final->user_focus = 1;
				}
				else
				{
					$obj_final ->user_focus = 0;
				}
				array_push($deal_info, $obj_final) ;
			}
			
			$obj->status = 200;
			$obj->data	 = $deal_info;
				
		}
		else
		{
			//筛选列表
				$obj->r = "没有符合筛选条件的项目";

		}
/************返回数据******************************/		

		CommonUtil::return_info($obj);	





	
	

?>