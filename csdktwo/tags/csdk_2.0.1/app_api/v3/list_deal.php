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

/*Parameters for Followed List, if it is equal to 1, which means the user is going to Followed List page*/
$focus 	= isset($_POST['focus'])? trim($_POST['focus']):NULL;

/*Parameters for investation intend List, if it is equal to 1, which means the user is going to investation intend List page*/
$intend 	= isset($_POST['intend'])? trim($_POST['intend']):NULL;



/************Checking whether investor is recognized or not*****************/
$sql_check_investor = "
						select id from cixi_user where user_type = 1 and is_review = 1 and id = ?
						";
$para_check_investor = array($uid);
$result_check_investor = PdbcTemplate::query($sql_check_investor,$para_check_investor);
if(empty($result_check_investor))
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
				select 	 distinct initiator.id 		initiator_id
						,initiator.is_review		initiator_isRview
						,initiator.province		initiator_province
						,deal.cate_choose		deal_cates			/******分类标签（返回分类标签）*********/
						,deal.id 				deal_id
						,deal.img_deal_logo 	image_deal_logo
						,deal.name			deal_name
						,deal.deal_sign 			deal_sign	
						,deal.view_count		view_count
						,deal.focus_count	focus_count
						,deal.period_id	period_id
						,deal.province	province
						,deal.city	city
						
				";
$sql_where 	= "
				where 	deal.user_id 		= initiator.id 					/*** ALWAYS THERE *********************/
				and   	initiator.is_review = 1 
				and   	deal.is_effect = 2 
				and   	deal.is_delete = 0 
			  ";
$sql_order_page	="
				order by 	deal.sort desc,deal.id desc
				limit 		$page_offset, $page_rows
				";
$sql_from 	= " 
					from 
						cixi_deal 				deal
						,cixi_user 				initiator
						
					";
			
/*********筛选项目列表****************/
if((!is_null($cate)||!is_null($district)||!is_null($period_id))&&(is_null($focus) && is_null($intend)))
{	
	if(!is_null($cate))
	{
		/*********Deal with Categories ***********************/
		$cate_array 	= explode('_',$cate);
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
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;
	
}
/*******关注列表***********/
if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(!is_null($focus)) && (is_null($intend)))
{	
	$sql_from = "
				from 
						cixi_deal 				deal
						,cixi_user 				initiator
						,cixi_deal_focus_log 	focus
				";
	$condition = "
				and 	focus.deal_id	= deal.id	
				and   	focus.user_id 	= ?	";		/******关注列表条件****/
	$sql_order_page	="
				order by 	focus.create_time desc
				limit 		$page_offset, $page_rows
				";
	$para_final[] = $uid;
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;
	//$obj->r	= "您目前没有关注的项目";
}
/*******投资意向列表***********/
if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (!is_null($intend)))
{
	$sql_select .= ", intend.create_time   intend_time";
	$sql_from 	= " 
					from 
						cixi_deal 				deal
						,cixi_user 				initiator
						,cixi_deal_intend_log 	intend
						
					";
	$condition = "
					and intend.deal_id = deal.id
					and intend.user_id = ?
					";
	$sql_order_page	="
				order by 	intend.create_time desc
				limit 		$page_offset, $page_rows
				";
	$para_final[] = $uid;
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;
	//$obj->r	= "您目前没有对任何项目有投资意向";
}
/*******默认列表************/

if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (is_null($intend)))
{
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$sql_order_page;
	//$obj->r	= "没有更多项目";
}

/******************Execute Sql statement **********************************/

$result = PdbcTemplate::query($sql_final,$para_final,PDO::FETCH_OBJ,0);
/**************  数据处理  ****************/		
//var_dump($result);
$deal_info = array();

		if(!empty($result))
		{
			foreach($result as $key => $val) {
				$obj_final = new stdClass;
				$obj_final->deal_id			= is_null($val->deal_id) ? ""		: $val->deal_id;
				$obj_final->image_deal_logo	= is_null($val->image_deal_logo) ? "" : $val->image_deal_logo;
				$obj_final->deal_name 				= is_null($val->deal_name) ? "" 				: $val->deal_name;
				$obj_final->view_count 		= is_null($val->view_count) ? 0 		: $val->view_count;
				$obj_final->focus_count 	= is_null($val->focus_count) ? 0	: $val->focus_count;
				$obj_final->deal_cates		= is_null($val->deal_cates) ? ""     	: $val ->deal_cates;
				$obj_final->deal_sign				= is_null($val->deal_sign) ? ""     		: $val ->deal_sign;
				$obj_final->period_id				= is_null($val->period_id) ? 0		: $val ->period_id;
				$obj_final->province				= is_null($val->province) ? 0	: $val ->province;
				$obj_final->city				= is_null($val->city) ? 0   		: $val ->city;
				if(isset($result[$key]->intend_time))
				{
					$obj_final->intend_time		= is_null($val->intend_time) ? ""     	: $val ->intend_time;
				}else
				{
					$obj_final->intend_time		= "";
				}

				//投資意向次數
				$intend_num ="
									select 	count(*) as count
									from   	cixi_deal_intend_log
									where 	deal_id = ?
									";

				$para_intend_num	= array($val->deal_id);
				$result_intend_num 		= PdbcTemplate::query($intend_num,$para_intend_num);
				$obj_final->intend_count =is_null($result_intend_num[0]->count) ? ""	:$result_intend_num[0]->count;
				
				//项目亮点
				$sql_point="select point_info from cixi_deal_sign_point where deal_id =".$obj_final->deal_id."  order by id asc limit 0,3";
				//$para_value_point[]=$obj_final->deal_id;
				$result_point= PdbcTemplate::query($sql_point);
				$obj_final->deal_sign_point=array();
				if(!empty($result_point)){
					foreach ($result_point as $k => $v) {
						$point_info 	= new stdClass ;	
						$point_info->title= is_null($v->point_info) ? "" : trim($v->point_info);
						if($point_info->title!="")
							{
								array_push($obj_final->deal_sign_point, $point_info) ;
							}
					}
				}
				array_push($deal_info, $obj_final) ;

			}
			
			$obj->status = 200;
			$obj->data	 = $deal_info;
				
		}
		else
		{
			//筛选列表
			if((!is_null($cate)||!is_null($district)||!is_null($period_id))&&(is_null($focus) && is_null($intend)))
			{
				$obj->r = "没有符合筛选条件的项目";
			}
			//关注列表
			if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(!is_null($focus)) && (is_null($intend)))
			{
				$obj->r = "没有关注的项目";
			}
			if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (!is_null($intend)))
			{
				$obj->r = "您尚无项目投资意向";
			}
			if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (is_null($intend)))
			{
				$obj->r = "没有更多项目";
			}

		}
/************返回数据******************************/		

		CommonUtil::return_info($obj);	
?>