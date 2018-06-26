<?php
require_once('base.php');
//基金   3.1号
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$uid	= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$sign_sn	= isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;

/* 我的投资列表，LP可见，参与投资的渠道合伙人参与 */

$invest_fund_sql="
			SELECT fund.*,
		    is_default_fund
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id 
			and user_type=1
		    and fund.is_delete=1 
		    and fund.status=2 
		    and fund.is_csdk_fund=1
		    and userfund.user_id=? 
			order by fund.establish_date desc,fund.id desc  ";

$invest_final = array($uid);
$invest_result = PdbcTemplate::query($invest_fund_sql,$invest_final,PDO::FETCH_OBJ,0);

/* 我的管理列表，投资总监可见 */
$manage_fund_sql="
			SELECT fund.*,
		    is_default_fund
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id 
			and user_type=4
		    and fund.is_delete=1 
		    and fund.status=2 
		    and fund.is_csdk_fund=1
		    and userfund.user_id=? 
			order by fund.establish_date desc,fund.id desc  ";

$manage_final = array($uid);
$manage_result = PdbcTemplate::query($manage_fund_sql,$manage_final,PDO::FETCH_OBJ,0);


/* 我协助发起的基金列表，渠道合伙人可见 */
$assist_fund_sql="
			SELECT fund.*,
		    is_default_fund
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id
			and user_type=3
		    and fund.is_delete=1
		    and fund.status=2
		    and fund.is_csdk_fund=1
		    and userfund.user_id=?
			order by fund.establish_date desc,fund.id desc  ";

$assist_final = array($uid);
$assist_result = PdbcTemplate::query($assist_fund_sql,$assist_final,PDO::FETCH_OBJ,0);
$obj->invest_fund =new stdClass;
$obj->manage_fund=new stdClass;
$obj->assist_fund = new stdClass;
	if(!empty($invest_result)||!empty($manage_result)||!empty($assist_result)){
			//投资的基金列表
		 	if(!empty($invest_result))
			{ 
				$obj_invest_fund_out = new stdClass;
				$obj_invest_fund_out->fund_title = '我投资的基金';
				$obj_invest_fund_out->user_type="1";
				$obj_invest_fund_out->fund_list = array();
				foreach ($invest_result as $key => $val) 
				{
					$fund_list = new stdClass; 
					$fund_list->fund_id = is_null($val->id) ? ""		: $val->id;
					$fund_list->fund_name = is_null($val->name) ? ""		: $val->name;
					$fund_list->fund_short_name = is_null ( $val->short_name ) ? "" : $val->short_name;
		 			$fund_list->is_default_fund = is_null($val->is_default_fund) ? ""		: $val->is_default_fund;
		 			array_push($obj_invest_fund_out->fund_list, $fund_list) ;
		   		}
		   		 $obj->invest_fund=$obj_invest_fund_out ;
		 	}
		 	 
		 	
		 	//管理的基金列表
		 	if(!empty($manage_result))
			{
			 
				$obj_manage_fund_out = new stdClass;
				$obj_manage_fund_out->fund_title = '我管理的基金';
				$obj_manage_fund_out->user_type="4";
				$obj_manage_fund_out->fund_list = array();
				foreach ($manage_result as $key => $val) 
				{
					$fund_list = new stdClass; 
					$fund_list->fund_id = is_null($val->id) ? ""		: $val->id;
					$fund_list->fund_name = is_null($val->name) ? ""		: $val->name;
					$fund_list->fund_short_name = is_null ( $val->short_name ) ? "" : $val->short_name;
		 			$fund_list->is_default_fund = is_null($val->is_default_fund) ? ""		: $val->is_default_fund;
		 			array_push($obj_manage_fund_out->fund_list, $fund_list) ;
		   		}
		   		$obj->manage_fund=$obj_manage_fund_out ;
		 	}
		 	 
		 	
		 	//创建的基金列表
		 	if(!empty($assist_result))
			{
			 
				$obj_assist_fund_out = new stdClass;
				$obj_assist_fund_out->fund_title = '我协助发起的基金';
				$obj_assist_fund_out->user_type="3";
				$obj_assist_fund_out->fund_list = array();
				foreach ($assist_result as $key => $val) 
				{
					$fund_list = new stdClass; 
					$fund_list->fund_id = is_null($val->id) ? ""		: $val->id;
					$fund_list->fund_name = is_null($val->name) ? ""		: $val->name;
					$fund_list->fund_short_name = is_null ( $val->short_name ) ? "" : $val->short_name;
		 			$fund_list->is_default_fund = is_null($val->is_default_fund) ? ""		: $val->is_default_fund;
		 			array_push($obj_assist_fund_out->fund_list, $fund_list) ;
		   		} 
		   		$obj->assist_fund=$obj_assist_fund_out ;
		 	}
		 	 
	}	 	
	else
	{
	$obj->r = "暂无数据";
	}
	//返回数据
	$obj->status = 200;
	CommonUtil::return_info($obj);	
	
?>