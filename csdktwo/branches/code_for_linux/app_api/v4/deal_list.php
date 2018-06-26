<?php
require_once ('base.php');
define ( "PAGE_SIZE", 5 );
/* verify user status for both investor and initiator */

$obj = new stdClass ();
$obj->status = 500;

$page_num = isset ( $_POST ['page'] ) ? trim ( $_POST ['page'] ) : 1;
$page_offset = PAGE_SIZE * $page_num - PAGE_SIZE;
$page_rows = PAGE_SIZE;

$uid = isset ( $_POST ['uid'] ) ? trim ( $_POST ['uid'] ) : '';
$sign_sn = isset ( $_POST ['sign_sn'] ) ? trim ( $_POST ['sign_sn'] ) : '';

/* Filter parameters used in Filter list, there are three dimension filter which are cate, district, period */
$cate = isset ( $_POST ['cate'] ) ? trim ( $_POST ['cate'] ) : NULL;
$district = isset ( $_POST ['district'] ) ? trim ( $_POST ['district'] ) : NULL;
$period_id = isset ( $_POST ['period_id'] ) ? trim ( $_POST ['period_id'] ) : NULL;
$focus_filter = isset ( $_POST ['focus_filter'] ) ? trim ( $_POST ['focus_filter'] ) : NULL;
$update_time = isset ( $_POST ['update_time'] ) ? trim ( $_POST ['update_time'] ) : NULL;
/* Parameters for investation intend List, if it is equal to 1, which means the user is going to investation intend List page */
$intend = isset ( $_POST ['intend'] ) ? trim ( $_POST ['intend'] ) : NULL;
$search = isset ( $_POST ['search'] ) ? trim ( $_POST ['search'] ) : NULL;
if(!empty($uid)&&!empty($sign_sn)&&! is_null ( $intend )){
 $user_status = CommonUtil::verify_user ();
 CommonUtil::check_status ( $user_status );
} 
/**
 * ***Initializing Variables Which Used In Following Sql Statement ****
 */
$sql_select = "";
$sql_from = "";
$sql_where = "";
$condition = "";
$sql_limit_condition = "";
$sql_final = "";
$para_final = array ();
$sql_select = "
					  select cate_choose 		deal_cates
				      ,id 		   		deal_id
					  ,img_deal_logo 	image_deal_logo
					  ,name				deal_name
					  ,deal_sign		deal_sign
					  ,comment_count	comment_count
					  ,period_id 		period_id
					  ,province			province
					  ,city				city
					  ,is_effect		is_effect
					  ,is_case 			is_case
				";

$sql_from = "from cixi_deal";

$sql_where = "
				where is_publish = 2  
					";

$sql_order_by_condition = "
							  order by is_effect asc,sort asc,  update_time desc";

/**
 * *******Now we are going to make filter functionality*********************
 */
/**
 * **Deal with Categories which are choosed by invetor*******
 */

if (! is_null ( $cate )) {
	$subQuery_get_deal_id_list = "select deal_id from cixi_deal_select_cate where cate_id = {$cate}";
	$condition .= "and id in ($subQuery_get_deal_id_list)";
}
if (! is_null ( $intend )) {
	$subQuery_get_intend_sql = "select deal.id as deal_id from cixi_deal_intend_log as intend,cixi_user as user,cixi_deal as deal where  intend.user_id=user.id and intend.deal_id=deal.id and intend.create_time is not null and intend.create_time<>0 and user.is_review=1 and user.is_effect=1 and deal.is_effect in(2,3) and user.id={$uid} ";
	$intend_sql = PdbcTemplate::query ( $subQuery_get_intend_sql, null, PDO::FETCH_OBJ, 0 );
	$intend_deal_id = "";
	foreach ( $intend_sql as $key => $value ) {
		$intend_deal_id = $intend_deal_id . $value->deal_id . ",";
	}
	$intend_deal_id = substr ( $intend_deal_id, 0, strlen ( $intend_deal_id ) - 1 );
	$condition .= "and id in ($intend_deal_id)";
}
if (! is_null ( $district )) {
	/**
	 * *******Deal with district**********************
	 */
	$pos = strpos ( $district, "_" );
	if ($pos === false) {
		$condition .= "and 	province  = ?  ";
		/**
		 * ****筛选列表条件（地区分类,省市）********
		 */
		$para_final [] = $district;
	} else {
		$district_array = explode ( '_', $district );
		$condition .= "and 	province  = ? 	
				   and 	city 	   = ? ";
		/**
		 * ****筛选列表条件（地区分类,省市）********
		 */
		$para_final [] = $district_array [0];
		$para_final [] = $district_array [1];
	}
}

if (! is_null ( $period_id )) {
	$period_id_list = "select 	id 		from	cixi_deal_period where mapname= (SELECT mapname from cixi_deal_period where id=?)";
	$para_period_id = array (
			$period_id 
	);
	$result_period_sql = PdbcTemplate::query ( $period_id_list, $para_period_id );
	$list_period_id = "";
	foreach ( $result_period_sql as $key => $value ) {
		$list_period_id = $list_period_id . $value->id . ",";
	}
	$list_period_id = substr ( $list_period_id, 0, strlen ( $list_period_id ) - 1 );
	$condition .= "and period_id in ({$list_period_id})";
}
/**
 * ********Deal with order based on comment_count**********************
 */

if (! is_null ( $focus_filter )) {
	$sql_order_by_condition = "order by is_effect asc,comment_count desc,create_time desc";
}
if (! is_null ( $update_time )) {
	$sql_order_by_condition = "order by is_effect asc,update_time desc,create_time desc";
}
if (! is_null ( $search )) {
	$condition .= "and name like '%{$search}%'";
} else {
/**
 * *************************************************
 */
	/*
	 * $sql_limit_condition .= "
	 * limit 		$page_offset, $page_rows
	 * ";
	 */
/**
 * *************************************************
 */
}

$condition .= "and is_delete = 0";
// $sql_final = $sql_select . " " . $sql_from . " " . $sql_where . " " . $condition . " " . $sql_order_by_condition;
// var_dump($sql_final);
$sql_final_investing = "";
$sql_final_invested = "";
$sql_case_common = "or ( is_case = 1  ";
$sql_case_common .= $condition . " " . ")";
$para_final_invt_case = array ();
if (! empty ( $para_final )) {
	for($double = 1; $double <= 2; $double ++) {
		for($i = 0; $i < count ( $para_final ); $i ++) {
			$para_final_invt_case [] = $para_final [$i];
		}
	}
}

$sql_final_investing = $sql_select . "  " . $sql_from . " " . $sql_where . " " . $condition . " " . $sql_order_by_condition;
/*
 * var_dump($sql_final_investing);
 * var_dump($sql_final_invested);
 *
 * var_dump($para_final_invt_case);
 */

/**
 * ****************Execute Sql statement *********************************
 */

$result_investing = PdbcTemplate::query ( $sql_final_investing, $para_final, PDO::FETCH_OBJ, 0 );

// $deal_info = array ();
$deal_info_final = array ();
$deal_info_investing = array ();
function get_deal_info_x($result, &$deal_info, $uid) {
	foreach ( $result as $key => $val ) {
		$obj_final = new stdClass ();
		$obj_final->deal_id = is_null ( $val->deal_id ) ? "" : $val->deal_id;
		$obj_final->image_deal_logo = is_null ( $val->image_deal_logo ) ? "" : $val->image_deal_logo;
		$obj_final->deal_name = is_null ( $val->deal_name ) ? "" : $val->deal_name;
		$obj_final->comment_count = is_null ( $val->comment_count ) ? 0 : $val->comment_count;
		$obj_final->deal_cates = is_null ( $val->deal_cates ) ? "" : $val->deal_cates;
		$obj_final->deal_sign = is_null ( $val->deal_sign ) ? "" : $val->deal_sign;
		$sql_period_name = "select name from cixi_deal_period where id='{$val->period_id}'";
		$period_name = PdbcTemplate::query ( $sql_period_name, null, PDO::FETCH_OBJ, 1 );
		$obj_final->period_name = is_null ( $period_name->name ) ? "" : $period_name->name;
		$obj_final->province = is_null ( $val->province ) ? 0 : $val->province;
		$obj_final->city = is_null ( $val->city ) ? 0 : $val->city;
		$obj_final->is_effect = is_null ( $val->is_effect ) ? 0 : $val->is_effect;
		$obj_final->is_case = is_null ( $val->is_case ) ? 0 : $val->is_case;
		
		$sql_focus = "select 1 from cixi_deal_focus_log where user_id = ? and deal_id = ?";
		$para_focus = array ();
		$para_focus [] = $uid;
		$para_focus [] = $val->deal_id;
		$result_focus = PdbcTemplate::query ( $sql_focus, $para_focus, PDO::FETCH_OBJ, 0 );
		if (! empty ( $result_focus )) {
			$obj_final->user_focus = 1;
		} else {
			$obj_final->user_focus = 0;
		}
		
		// 投資意向次數
		$intend_num = "
									select 	count(*) as count
									from   	cixi_deal_intend_log
									where 	deal_id = ?
									";
		$para_intend_num = array (
				$val->deal_id 
		);
		$result_intend_num = PdbcTemplate::query ( $intend_num, $para_intend_num );
		$obj_final->intend_count = is_null ( $result_intend_num [0]->count ) ? "" : $result_intend_num [0]->count;
		array_push ( $deal_info, $obj_final );
	}
}

if (! empty ( $result_investing )) {
	if (! empty ( $result_investing )) {
		get_deal_info_x ( $result_investing, $deal_info_investing, $uid );
		
		for($i = 0; $i < count ( $deal_info_investing ); $i ++) {
			$deal_info_final [] = $deal_info_investing [$i];
		}
	}
	
	$obj->status = 200;
	$obj->data = $deal_info_final;
	// var_dump($deal_info_final);
	// return;
} 

else if (empty ( $result_investing )) {
	// 筛选列表
	
	if ((! is_null ( $cate ) || ! is_null ( $district ) || ! is_null ( $period_id ) || ! is_null ( $focus_filter )|| ! is_null ( $search )) && (! is_null ( $intend ))) {
		$obj->r = "没有符合筛选条件的项目";
	}
	if ((is_null ( $cate ) && is_null ( $district ) && is_null ( $period_id )) && is_null ( $intend ) && is_null ( $focus_filter ) && is_null ( $search )) {
		$obj->r = "没有更多项目";
	}
	if ((! is_null ( $cate ) || ! is_null ( $district ) || ! is_null ( $period_id ) || ! is_null ( $focus_filter ) || ! is_null ( $search )) && is_null ( $intend )) {
		$obj->r = "没有符合筛选条件的项目";
	}
	if ((is_null ( $cate ) && is_null ( $district ) && is_null ( $period_id ) && is_null ( $focus_filter )&& is_null ( $search )) && (! is_null ( $intend ))) {
		// 身份验证
		$user_intend = "select intend.id from cixi_deal_intend_log as intend ,cixi_deal as deal  where  deal.id=intend.deal_id and deal.is_effect in (2,3) and intend.user_id=? ";
		$para_user_intend = array (
				$uid 
		);
		$result_check_user_intend = PdbcTemplate::query ( $user_intend, $para_user_intend );
		if (empty ( $result_check_user_intend )) {
			$obj->r = "您尚未发起任何投资意向，马上去【平台项目】看看有什么感兴趣的项目";
		} else {
			$obj->r = "没有更多项目";
		}
	}
}
/**
 * **********返回数据*****************************
 */

CommonUtil::return_info ( $obj );

?>