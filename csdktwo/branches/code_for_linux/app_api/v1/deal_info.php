<?php
require_once('base.php');
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$obj = new stdClass;
$obj->status = 500;

$add_summary = isset($_POST['add_summary'])? trim($_POST['add_summary']):NULL;
$deal_id = isset($_POST['deal_id'])?trim($_POST['deal_id']):NULL;
$uid		= trim($_POST['uid']);
//$type = isset($_POST['app_type'])? trim($_POST['app_type']):NULL;
if (is_null($deal_id)) {
	$obj->r = "项目ID为空";
	CommonUtil::return_info($obj);
	return;	
}
/**********获取项目信息******************/
$sql_select = "
				select id 	    deal_id
				,name 			deal_name
				,deal_brief  	deal_brief
				,company_name	company_name
				,company_brief	company_brief
				,company_title	company_title
				,pe_amount_plan		pe_amount_plan
				,pe_sell_scale		pe_sell_scale
				,pe_least_amount	pe_least_amount
				,business_mode		business_mode
				,bp_url 			bp_url
				,deal_url 			deal_url
				,entry_info 			entry_info
				,solve_pain_info 			solve_pain_info
				,recommend_reason 			recommend_reason
				,operation_info 			operation_info
				,mark_data_info 			mark_data_info
				,achievement_info 			achievement_info
				,img_achievement 			img_achievement
				,vision_info 			vision_info
				,img_vision 			img_vision
				,financing_info 			financing_info
				,interview_time 			interview_time
				,profession_info 			profession_info
				,financing_amount 			financing_amount
				";
/**********如果add_summary =1 则获取更多项目信息******************/
if(1==$add_summary)
{
	$sql_select .= "
				,deal.deal_sign		deal_sign		
					,deal.cate_choose		deal_cates		
					,deal.img_deal_logo 	img_deal_logo
					,deal.view_count		view_count
					,deal.focus_count		focus_count
					,deal.period_id     	 period_id
					,deal.province			province
					";
}
$sql_deal_rest = "
		from	cixi_deal	deal
		where 	id = ?
		";
$sql_deal = $sql_select." ".$sql_deal_rest;
$para_deal=array($deal_id);

$r_deal = PdbcTemplate::query($sql_deal,$para_deal,PDO::FETCH_OBJ, 1);
/**********获取团队信息******************/

$sql_team = "
				select img_logo	img_logo
					,name	name
					,title		title
					,brief 		brief
				from cixi_deal_team  
				where deal_id = ? 
				order by id asc
			";
$para_team = array($deal_id);
$r_team = PdbcTemplate::query($sql_team,$para_team);

/**********获取项目大事件信息******************/
$sql_event = "
				select create_time	create_time
						,brief		brief
				from	cixi_deal_event
				where	deal_id = ?  
				order by create_time desc
		";
$para_event = array($deal_id);
$r_event = PdbcTemplate::query($sql_event,$para_event);

/**********获取交易事件信息******************/

$sql_finance_event = "
				select 	create_time	create_time
						,brief		brief
						,period		period
						,price 			price
				from	cixi_deal_trade_event
				where	deal_id = ? 
				order by create_time asc
		";
$para_finance_event = array($deal_id);
$r_finance_event = PdbcTemplate::query($sql_finance_event,$para_finance_event);

$sql_recommend="select recommend_person,recommend_info from cixi_deal_recommend  where deal_id = ? order by id asc";
$para_recommend = array($deal_id);
$r_recommend = PdbcTemplate::query($sql_recommend,$para_recommend);

//var_dump($r_recommend);
//var_dump($r_finance_event);
/**********判断此项目是否已经被关注******************/
	$obj->deal_team 	= array() ;
	$obj->deal_event = array() ;
	$obj->trade_event = array() ;
	
$followed_exists ="
					select 	'x'
					from   	cixi_deal_focus_log
					where 	deal_id = ?
					and 	user_id = ?
					";

$para_followed_exists 	= array($deal_id,$uid);
$r_followed_exists 		= PdbcTemplate::query($followed_exists,$para_followed_exists);
	if(!empty($r_followed_exists))
	{
		$obj->is_focus = 1;
	}
	else
	{
		$obj->is_focus = 0;
	}
/***************投资意向****************************/

$intend_exists ="
					select 	create_time
					from   	cixi_deal_intend_log
					where 	deal_id = ?
					and 	user_id = ?
					";

$para_intend_exists	= array($deal_id,$uid);
$result_intend 		= PdbcTemplate::query($intend_exists,$para_intend_exists);

//投資意向次數
$intend_num ="
					select 	count(*) as count
					from   	cixi_deal_intend_log
					where 	deal_id = ?
					";

$para_intend_num	= array($deal_id);
$result_intend_num 		= PdbcTemplate::query($intend_num,$para_intend_num);

/************拼接数据***************************/
	
	//var_dump($r_deal);
    if(!empty($r_deal))
    {
    	$obj->status = 200;
		$obj->deal_id =is_null($r_deal->deal_id) ? ""	:$r_deal->deal_id;
		$obj->deal_name =is_null($r_deal->deal_name) ? ""	:$r_deal->deal_name;
		//$obj->deal_sign =is_null($r_deal->deal_sign) ? ""	:$r_deal->deal_sign;
		$obj->deal_brief =is_null($r_deal->deal_brief) ? ""	:$r_deal->deal_brief;
		$obj->company_title =is_null($r_deal->company_title) ? ""	:$r_deal->company_title;
		$obj->company_name =is_null($r_deal->company_name) ? ""	:$r_deal->company_name;
		$obj->company_brief =is_null($r_deal->company_brief) ? ""	:$r_deal->company_brief;
		$obj->pe_amount_plan =is_null($r_deal->pe_amount_plan) ? ""	:$r_deal->pe_amount_plan;
		$obj->pe_sell_scale =is_null($r_deal->pe_sell_scale) ? ""	:$r_deal->pe_sell_scale;
		$obj->pe_least_amount =is_null($r_deal->pe_least_amount) ? ""	:$r_deal->pe_least_amount;
		$obj->business_mode =is_null($r_deal->business_mode) ? ""	:$r_deal->business_mode;
		//$obj->bp_url =is_null($r_deal->bp_url) ? ""	:$r_deal->bp_url;
		$obj->deal_url =is_null($r_deal->deal_url) ? ""	:$r_deal->deal_url;
		$obj->entry_info =is_null($r_deal->entry_info) ? ""	:$r_deal->entry_info;
		$obj->solve_pain_info =is_null($r_deal->solve_pain_info) ? ""	:$r_deal->solve_pain_info;
		$obj->recommend_reason =is_null($r_deal->recommend_reason) ? ""	:$r_deal->recommend_reason;
		$obj->operation_info =is_null($r_deal->operation_info) ? ""	:$r_deal->operation_info;
		$obj->mark_data_info =is_null($r_deal->mark_data_info) ? ""	:$r_deal->mark_data_info;
		$obj->achievement_info =is_null($r_deal->achievement_info) ? ""	:$r_deal->achievement_info;
		$obj->img_achievement =is_null($r_deal->img_achievement) ? ""	:$r_deal->img_achievement;
		$obj->vision_info =is_null($r_deal->vision_info) ? ""	:$r_deal->vision_info;
		$obj->img_vision =is_null($r_deal->img_vision) ? ""	:$r_deal->img_vision;
		$obj->financing_info =is_null($r_deal->financing_info) ? ""	:$r_deal->financing_info;
		$obj->interview_time =is_null($r_deal->interview_time) ? ""	:$r_deal->interview_time;
		$obj->profession_info =is_null($r_deal->profession_info) ? ""	:$r_deal->profession_info;
		$obj->financing_amount =is_null($r_deal->financing_amount) ? ""	:$r_deal->financing_amount;
		$obj->intend_count =is_null($result_intend_num[0]->count) ? ""	:$result_intend_num[0]->count;
		/*if ($type=='mac') {
			$obj->bp_url=CommonUtil::getQiniuPath($obj->bp_url);
		}else{
			$obj->bp_url=PDF_DOMAIN."bp_viewer/get_bp.php?key=".$obj->bp_url;
		}*/
		$obj->bp_url =is_null($r_deal->bp_url) ? ""	:PDF_DOMAIN."bp_viewer/get_bp.php?key=".$r_deal->bp_url."&type=app";
		//$obj->bp_url=PDF_DOMAIN."bp_viewer/get_bp.php?key=".$obj->bp_url;
		
		if (!empty($result_intend)) {
			$obj->intend_time =is_null($result_intend[0]->create_time) ? ""	:$result_intend[0]->create_time;
		}

		if(1==$add_summary)
		{
			$obj->province =is_null($r_deal->province) ? ""	:$r_deal->province;
			$obj->deal_sign =is_null($r_deal->deal_sign) ? ""	:$r_deal->deal_sign;
			$obj->deal_cates =is_null($r_deal->deal_cates) ? ""	:$r_deal->deal_cates;
			$obj->image_deal_logo =is_null($r_deal->img_deal_logo) ? ""	:$r_deal->img_deal_logo;
			$obj->view_count =is_null($r_deal->view_count) ? ""	:$r_deal->view_count;
			$obj->focus_count =is_null($r_deal->focus_count) ? ""	:$r_deal->focus_count;
			$obj->period_id =is_null($r_deal->period_id) ? ""	:$r_deal->period_id;

				//项目亮点
				$sql_point="select point_info from cixi_deal_sign_point where deal_id =".$r_deal->deal_id." order by id asc limit 0,3";
				$result_point= PdbcTemplate::query($sql_point);
				$obj->deal_sign_point=array();
				if(!empty($result_point)){
					foreach ($result_point as $k => $v) {
							$point_info 	= new stdClass ;	
							$point_info->title= is_null($v->point_info) ? "" : $v->point_info;
							if($point_info->title!="")
							{
								array_push($obj->deal_sign_point, $point_info) ;
							}
					}
				}
		}
	}
	else
	{
			$obj->r = "项目不存在";
		    CommonUtil::return_info($obj);
		    return;	
	}
	
	if(!empty($r_team))
	{
		foreach($r_team as $key => $val)
		{
				$obj_team 			= new stdClass ;
				//$obj_team->t_id =is_null($val->t_id) ? ""	:$val->t_id;
				$obj_team->img_logo =is_null($val->img_logo) ? ""	:$val->img_logo;
				$obj_team->name =is_null($val->name) ? ""	:$val->name;
				$obj_team->title =is_null($val->title) ? ""	:$val->title;
				$obj_team->brief =is_null($val->brief) ? ""	:$val->brief;
				array_push($obj->deal_team, $obj_team) ;
		}
	}
	if(!empty($r_event))
	{
		foreach ($r_event as $key => $val) 
		{
				$obj_event 				= new stdClass ;
				//$obj_event->t_id =is_null($val->t_id) ? ""	:$val->t_id;
				$obj_event->create_time =is_null($val->create_time) ? ""	:$val->create_time;
				$obj_event->brief =is_null($val->brief) ? ""	:$val->brief;
				array_push($obj->deal_event, $obj_event) ;
		}
	}
	if(!empty($r_finance_event))
	{
		foreach($r_finance_event as $key => $val)
		{
				$obj_finance_event = new stdClass;
				//$obj_finance_event ->t_id = is_null($val->t_id) ? 0: $val -> t_id;
				$obj_finance_event ->create_time = is_null($val->create_time) ? "" : $val -> create_time;
				$obj_finance_event ->brief = is_null($val->brief) ? "" : $val ->brief;
				$obj_finance_event ->period = is_null($val->period) ? "" : $val ->period;
				$obj_finance_event ->price = is_null($val->price) ? "" : $val ->price;
				array_push($obj->trade_event, $obj_finance_event) ;
				
		}
	}
	if(!empty($r_recommend))
	{	$obj->deal_recommend=array();
		foreach($r_recommend as $key => $val)
		{
				$obj_deal_recommend = new stdClass ;
				$obj_deal_recommend->recommend_person =is_null($val->recommend_person) ? ""	:$val->recommend_person;
				$obj_deal_recommend->recommend_info =is_null($val->recommend_info) ? ""	:$val->recommend_info;
				array_push($obj->deal_recommend, $obj_deal_recommend) ;
				
		}
	}


	//增加用户的项目亮点
	$sql_point="select point_info from cixi_deal_brief_point where deal_id =".$deal_id." order by id asc";
	$result_point= PdbcTemplate::query($sql_point);
	$obj->deal_brief_point=array();
	if(!empty($result_point)){
		foreach ($result_point as $k => $v) {
			$point_info 	= new stdClass ;	
			$point_info->title= is_null($v->point_info) ? "" : $v->point_info;
			array_push($obj->deal_brief_point, $point_info) ;
			}
	}

      {
      	//增加浏览次数
		$sql="select id from cixi_deal_visit_log where deal_id = ? and user_id = ?";//and ".time()." - create_time < 600";
   			$para_value[]=$deal_id;
   			$para_value[]=$uid;
   			$result=  PdbcTemplate::query($sql,$para_value,PDO::FETCH_OBJ, 1);
   			if (empty($result))
   		    {
				$sql="insert into cixi_deal_visit_log (deal_id,user_id,client_ip,create_time) values (?,?,?,?)";
				$param_visit[]=$deal_id;
				$param_visit[]=$uid;
				$param_visit[]="";//get_client_ip();
				$param_visit[]=time();
				$result_visit=PdbcTemplate::execute($sql,$param_visit,PDO::FETCH_OBJ, 1);
				//var_dump($result_visit);
				if($result_visit[0]==true)
				{
					$sql="update cixi_deal set view_count = view_count + 1 where id = ?";
					$para[]=$deal_id;
					PdbcTemplate::execute($sql,$para,PDO::FETCH_OBJ, 1);
				}
   		 	}
   		 }

	CommonUtil::return_info($obj);
?>