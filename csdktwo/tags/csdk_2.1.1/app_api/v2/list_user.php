<?php
require_once('base.php');
define("PAGE_SIZE", 5);

$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
/************Checking whether investor is recognized or not*****************/
$obj = new stdClass;
$obj->status = 500;

$page_num 	= isset($_POST['page'])?trim($_POST['page']):1;
$page_offset= PAGE_SIZE*$page_num - PAGE_SIZE;
$page_rows 	= PAGE_SIZE;

$uid		= trim($_POST["uid"]) ;
$sign_sn   	= trim($_POST["sign_sn"]) ;


$cate 		= isset($_POST['cate'])? trim($_POST['cate']):NULL; 
$district 	= isset($_POST['district'])? trim($_POST['district']):NULL;
$period_id 	= isset($_POST['period_id'])? trim($_POST['period_id']):NULL;
$isfocus 			= isset($_POST['isfocus'])? trim($_POST['isfocus']):NULL;


$sql_check_initiator = "
						select id from cixi_user where user_type = 0 and is_review = 1 and id = ?
						";
$para_check_initiator = array($uid);
$result_check_initiator = PdbcTemplate::query($sql_check_initiator,$para_check_initiator);
if(empty($result_check_initiator))
{
	$obj->status = 501;
	$obj->r = "您还未认证";
	CommonUtil::return_info($obj);	
	return;
}

/*****Initializing Variables Which Used In Following Sql Statement *****/
	$sql_select	= "";
	$sql_from	= "";
	$sql_where 	= "";
	$condition 	= "";
	$sql_final 	= "";
	$para_final = array();
/*************************/
$sql_select = "
				select 
						user_all.id 					user_id
						, user_all.img_user_logo 		img_user_logo
						, user_all.user_name 		user_name
						, user_all.per_sign 				per_sign
						, user_all.title 				title
						, user_all.intend_count 		intend_count
						, user_all.focus_count 			focus_count
						, investor.org_name      	org_name
						, investor.cate_choose 			investor_cate_choose
						, investor.period_choose 		investor_period_choose
						, investor.user_id 				investor_user_id
						, user_all.province 			province
						, user_all.city 				city
				 
				";
$sql_from  = "	from 	cixi_user 						user_all
						, cixi_user_ex_investor 		investor";

$sql_where = "where 	investor.user_id = user_all.id
				and 	user_all.is_review = 1
				and 	user_all.user_type = 1";

$sql_order_page	="
				GROUP BY USER_all.id
				order by 	user_all.id desc
				limit 		$page_offset, $page_rows
				";

/**************投资人列表*****************************/
/**************投资人筛选列表*****************************/
if(!is_null($cate)||!is_null($district)||!is_null($period_id)&&is_null($isfocus))
{
		if(!is_null($cate))
		{
		//$sql_from  .= " ,cixi_user_select_cate 		cate";                 /****修改***********************/
		/*********Deal with Categories ***********************/
		$cate_array 	= explode('_',$cate);
		$cates_condition 		= '';
		foreach($cate_array as $item_in_array)
		{		
			$cates_condition 	= $cates_condition.$item_in_array.',';	
		}
		$cate_condition=substr($cates_condition,0,-1);
		//$condition .= "and cate.cate_id IN($cate_condition)";/******筛选列表条件（方向）*********/

		$subQuery_user_id_list = "select user_id from cixi_user_select_cate where cate_id in ($cate_condition)";
		$condition .= " and user_all.id in ($subQuery_user_id_list)";/******筛选列表条件（方向）*********/
		}

		if(!is_null($district))
		{
			/*********Deal with district***********************/
			$pos = strpos($district, "_");
			if ($pos===false) {
				$condition .= "and 	user_all.province  = ?  ";		/******筛选列表条件（地区分类,省市）*********/
				$para_final[]= $district;
			}else{
				$district_array = explode('_',$district);
				$condition .= "and 	user_all.province  = ? 	
						   and 	user_all.city 	   = ? ";		/******筛选列表条件（地区分类,省市）*********/
				$para_final[]= $district_array[0];
				$para_final[]= $district_array[1];
			}			
		}
		if(!is_null($period_id))
		{
		//$sql_from  .= " ,cixi_user_select_cate 		cate";                 /****修改***********************/
		/*********Deal with Categories ***********************/
		$period_id_array 	= explode('_',$period_id);
		$period_id_condition 		= '';
		foreach($period_id_array as $item_in_array)
		{		
			$period_id_condition 	= $period_id_condition.$item_in_array.',';	
		}
		$period_id_condition=substr($period_id_condition,0,-1);
		//$condition .= "and cate.cate_id IN($cate_condition)";/******筛选列表条件（方向）*********/

		$subQuery_user_id_list = "select user_id from cixi_user_select_period where period_id in ($period_id_condition)";
		$condition .= " and user_all.id in ($subQuery_user_id_list)";/******筛选列表条件（方向）*********/
		}

}


/*******投资人被关注列表***********/
              /******  创业者已经关注的投资人列表， 需要创业者ID， 创业者与投资人的关系表
									*******************/
if(1==$isfocus&&(is_null($cate)&&is_null($district)&&is_null($period_id)))
{
	$sql_select	.=",focus.focus_user_id  user_id";
	$sql_from	.= ",cixi_user_focus_log focus";
	$condition .= " 
						and focus.focus_user_id = investor.user_id
						and focus.user_id = ?
						";
	$sql_order_page	="
				GROUP BY USER_all.id
				order by 	focus.create_time desc
				limit 		$page_offset, $page_rows
				";
	$para_final[]= $uid;
}
$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;
//var_dump($sql_final);

//var_dump($sql_final);

$result = PdbcTemplate::query($sql_final,$para_final,PDO::FETCH_OBJ,0);


/**************  数据处理  ****************/		


        $user_info = array();
		if(!empty($result))
		{
			foreach($result as $key => $val) {
				$obj_final = new stdClass;
				$obj_final->user_id			= is_null($val->user_id) ? "" 		: $val->user_id;
				$obj_final->img_user_logo	= is_null($val->img_user_logo) ? "" 	: $val->img_user_logo;
				$obj_final->user_name 			= is_null($val->user_name) ? "" 	: $val->user_name;
				$obj_final->per_sign 	= is_null($val->per_sign) ? "" 		: $val->per_sign;
				$obj_final->title				= is_null($val->title) ? ""	: $val->title;
				$obj_final->intend_count		= is_null($val->intend_count) ? ""     	: $val ->intend_count;
				$obj_final->focus_count			= is_null($val->focus_count) ? ""     		: $val ->focus_count;
				$obj_final->org_name		= is_null($val->org_name) ? ""     		: $val ->org_name;
				$obj_final->cates	= is_null($val->investor_cate_choose) ? ""     : $val ->investor_cate_choose;
				$obj_final->periods	= is_null($val->investor_period_choose) ? ""   : $val ->investor_period_choose;
				$obj_final->province	= is_null($val->province) ? ""   : $val ->province;
				$obj_final->city	= is_null($val->city) ? ""   : $val ->city;
				//$obj_final->investor_user_id		= is_null($val->investor_user_id) ? ""   : $val ->investor_user_id;
				

				//投资人亮点
				$sql_style="select point_info from cixi_investor_point where user_id = ".$obj_final->user_id." limit 0,3";
				//$para_value_style[]=$obj_final->user_id;
				$result_style = PdbcTemplate::query($sql_style);
				$obj_final->investor_point=array();
				if(!empty($result_style)){
					foreach ($result_style as $k => $v) {
						$style_info 	= new stdClass ;	
						$style_info->title= is_null($v->point_info) ? "" : trim($v->point_info);
						if($style_info->title!="")
							{
								array_push($obj_final->investor_point, $style_info);
							}
					}
				}

	
				array_push($user_info, $obj_final) ;
			}
			

			$obj->status = 200;
			$obj->data	 = $user_info;
				
		}
		else
		{
			//app不弹窗，此处提示信息暂不修改
			$obj->r	= "投资人不存在";
		}
/************返回数据******************************/		
		CommonUtil::return_info($obj);	
?>