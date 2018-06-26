<?php
require_once('base.php');
//基金   3.2号
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$uid	= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$sign_sn	= isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;
$fund_id	= isset($_POST['fund_id'])? trim($_POST['fund_id']):NULL;
$deal_id	= isset($_POST['deal_id'])? trim($_POST['deal_id']):NULL;
$para_final = array();
$deal_final = array();
$lastest_final = array();
$event_list_final = array();
$para_final_csdk = array();
/**********获取磁斯达克最新融资信息******************/
$event_sql="
			SELECT period,investor_amount,investor_before_evalute,investor_after_evalute FROM cixi_deal_trade_event AS event  WHERE `event`.deal_id=? and is_csdk_partake=1 and partake_fund=?
ORDER BY investor_time desc,id DESC LIMIT 0,1";
$para_final[] = $deal_id; 
$para_final[] = $fund_id;
$result_event = PdbcTemplate::query($event_sql,$para_final,PDO::FETCH_OBJ,0);

/**********获取项目信息******************/
$deal_sql="
			 SELECT id,name,s_name,company_name,com_addr,com_legal,com_web,com_reg_found,com_tel,com_busi,com_time,entry_info,out_plan
			 FROM cixi_deal AS deal  WHERE id=? and is_effect in(2,3)";
$deal_final[] = $deal_id;
$result_deal = PdbcTemplate::query($deal_sql,$deal_final,PDO::FETCH_OBJ,0);

/**********获取磁斯达克最新融资投资金额******************/
$csdk_sql="			SELECT investor_amount as csdk_investor_amount FROM cixi_deal_event_investor  where is_csdk_partake=1 and event_id=(SELECT id FROM cixi_deal_trade_event AS event  WHERE `event`.deal_id=? and is_csdk_partake=1 and partake_fund=?
ORDER BY investor_time desc,id DESC LIMIT 0,1) ORDER BY create_time desc,id DESC LIMIT 0,1";
$para_final_csdk[] = $deal_id;
$para_final_csdk[] = $fund_id;
$result_csdk = PdbcTemplate::query($csdk_sql,$para_final_csdk,PDO::FETCH_OBJ,1);
 

/**********获取此项目最新的投资金额和投资回报******************/
$lastest_sql="
			SELECT investor_after_evalute,investor_payback FROM cixi_deal_trade_event AS event  WHERE `event`.deal_id=?  ORDER BY investor_time desc,id DESC LIMIT 0,1";
$lastest_final[] = $deal_id; 
$result_lastest = PdbcTemplate::query($lastest_sql,$lastest_final,PDO::FETCH_OBJ,1);
 

/**********获取融资记录信息******************/
$event_list_sql="
			SELECT id,investor_time,period,investor_amount,investor_before_evalute,investor_after_evalute,investor_rate,is_csdk_partake FROM cixi_deal_trade_event AS event  WHERE `event`.deal_id=?  ORDER BY investor_time asc,id asc ";
$event_list_final[] = $deal_id; 
$result_event_list = PdbcTemplate::query($event_list_sql,$event_list_final,PDO::FETCH_OBJ,0);

/**********获取团队信息******************/
$sql_team = "
				select img_logo,name,title,brief from cixi_deal_team where deal_id = ? order by id asc";
$para_team = array($deal_id);
$r_team = PdbcTemplate::query($sql_team,$para_team);
 
/**********获取这个项目新闻信息******************/
$sql_deal_news = "
				select id,n_title,n_list_img,n_brief,n_source,create_time from cixi_news where n_deal = ? and n_publish_state=2 and n_channel in (1,3) order by id desc LIMIT 0,5";
$para_deal_news = array($deal_id);
$r_deal_news = PdbcTemplate::query($sql_deal_news,$para_deal_news);

/**********获取项目行业新闻信息******************/
$sql_cate_news = "
				select id,n_title,n_list_img,n_brief,n_source,create_time from cixi_news where  n_publish_state=2 and n_channel in (1,3) 
				and n_cate in (select cate_id from cixi_deal_select_cate where deal_id=? ) and id not in(select id from cixi_news where n_deal = ? and n_publish_state=2 and n_channel in (1,3)) order by id desc LIMIT 0,5";
$para_cate_news = array($deal_id,$deal_id);
$r_cate_news = PdbcTemplate::query($sql_cate_news,$para_cate_news);
 
	if(!empty($result_deal))
	{
		foreach ($result_event as $key => $val) 
		{
			$obj->investor_period = is_null($val->period) ? ""		: $val->period;
			$obj->investor_total_amount = is_null(number_format($val->investor_amount)) ? ""		: number_format($val->investor_amount);
			$obj->investor_before_evalute = is_null(number_format($val->investor_before_evalute)) ? ""		: number_format($val->investor_before_evalute);
			$obj->investor_after_evalute = is_null(number_format($val->investor_after_evalute)) ? ""		: number_format($val->investor_after_evalute);
  		}
  		foreach ($result_deal as $key => $val) 
		{
 			$obj->deal_id = is_null($val->id) ? ""		: $val->id;
			$obj->deal_name = is_null($val->name) ? ""		: $val->name;
			$obj->deal_s_name = is_null($val->s_name) ? ""		: $val->s_name;
			$obj->company_name = is_null($val->company_name) ? ""		: $val->company_name;
			$obj->com_addr = is_null($val->com_addr) ? ""		: $val->com_addr;
			$obj->com_legal = is_null($val->com_legal) ? ""		: $val->com_legal;
			$obj->com_web = is_null($val->com_web) ? ""		: $val->com_web;
			$obj->com_reg_found = is_null(number_format($val->com_reg_found)) ? ""		: number_format($val->com_reg_found);
			$obj->com_tel = is_null($val->com_tel) ? ""		: $val->com_tel;
			$obj->com_busi = is_null($val->com_busi) ? ""		: $val->com_busi;
			$obj->com_time = is_null($val->com_time) ? ""		: $val->com_time;
			$obj->entry_info = is_null($val->entry_info) ? ""		: $val->entry_info;
			$obj->out_plan = is_null($val->out_plan) ? ""		: $val->out_plan;
   		}
   		$obj->investor_record = array(); 
		if(!empty($result_event_list)){
					foreach ($result_event_list as $k => $v) {
						$obj_record 	= new stdClass ;
						$obj_record->id = is_null($v->id) ? ""		: $v->id;
						$obj_record->investor_time = is_null($v->investor_time) ? ""		: $v->investor_time;
						$obj_record->period = is_null($v->period) ? ""		: $v->period;
						$obj_record->investor_amount = is_null(number_format($v->investor_amount)) ? ""		: number_format($v->investor_amount);
						$obj_record->investor_before_evalute = is_null(number_format($v->investor_before_evalute)) ? ""		: number_format($v->investor_before_evalute);
						$obj_record->investor_after_evalute = is_null(number_format($v->investor_after_evalute)) ? ""		: number_format($v->investor_after_evalute);
						$obj_record->investor_rate = is_null(floatval($v->investor_rate)) ? ""		: (string)(floatval($v->investor_rate)); 
						$obj_record->is_csdk_partake = is_null($v->is_csdk_partake) ? ""		: $v->is_csdk_partake;
 
								/**********获取投资机构信息******************/
					            $sql_event_investor="	
					            select s_name,investor_amount,investor_rate from cixi_deal_event_investor where  event_id=".$obj_record->id." order by create_time desc ,investor_amount desc ,id desc ";  
					            $result_event_investor= PdbcTemplate::query($sql_event_investor);
									$obj_record->investor_organization = array(); 
								if(!empty($result_event_investor)){
									foreach ($result_event_investor as $k => $v) {
										$obj_event_investor 	= new stdClass ;
 										$obj_event_investor->organization_name = is_null($v->s_name) ? ""		: $v->s_name;
										$obj_event_investor->investor_amount = is_null(number_format($v->investor_amount)) ? ""		: number_format($v->investor_amount);
										$obj_event_investor->investor_rate = is_null(floatval($v->investor_rate)) ? ""		:(string)(floatval( $v->investor_rate));
										array_push($obj_record->investor_organization, $obj_event_investor) ;
										}
								}
    					array_push($obj->investor_record, $obj_record) ;
 					}
		} 
		if(!empty($r_team))
		{	
		$obj->deal_team 	= array() ;
		foreach($r_team as $key => $val)
		{
				$obj_team 			= new stdClass ;
				$obj_team->img_logo =is_null($val->img_logo) ? ""	:$val->img_logo;
				$obj_team->name =is_null($val->name) ? ""	:$val->name;
				$obj_team->title =is_null($val->title) ? ""	:$val->title;
				$obj_team->brief =is_null($val->brief) ? ""	:$val->brief;
				array_push($obj->deal_team, $obj_team) ;
		}
		}

		if(!empty($r_deal_news))
		{	
		$obj->deal_news 	= array() ;
		foreach($r_deal_news as $key => $val)
		{
				$obj_deal_news= new stdClass ;
				$obj_deal_news->id =is_null($val->id) ? ""	:$val->id;
 				$obj_deal_news->n_title =is_null($val->n_title) ? ""	:$val->n_title;
				$obj_deal_news->n_list_img =is_null($val->n_list_img) ? ""	:$val->n_list_img;
				$obj_deal_news->n_brief =is_null($val->n_brief) ? ""	:$val->n_brief;
				$obj_deal_news->n_source =is_null($val->n_source) ? ""	:$val->n_source;
				$obj_deal_news->create_time =is_null($val->create_time) ? ""	:$val->create_time;
				array_push($obj->deal_news, $obj_deal_news) ;
		}
		}else{
			$obj->deal_news 	= array() ;
		}
		
		if(!empty($r_cate_news))
		{	
		$obj->cate_news 	= array() ;
		foreach($r_cate_news as $key => $val)
		{
				$obj_cate_news= new stdClass ;
				$obj_cate_news->id =is_null($val->id) ? ""	:$val->id;
 				$obj_cate_news->n_title =is_null($val->n_title) ? ""	:$val->n_title;
				$obj_cate_news->n_list_img =is_null($val->n_list_img) ? ""	:$val->n_list_img;
				$obj_cate_news->n_brief =is_null($val->n_brief) ? ""	:$val->n_brief;
				$obj_cate_news->n_source =is_null($val->n_source) ? ""	:$val->n_source;
				$obj_cate_news->create_time =is_null($val->create_time) ? ""	:$val->create_time;
				array_push($obj->cate_news, $obj_cate_news) ;
		}
		}
		else{
			$obj->cate_news 	= array() ;
		}
		if(!empty($result_csdk)){
    	$obj->csdk_investor_amount=is_null(number_format($result_csdk->csdk_investor_amount)) ? ""	:number_format($result_csdk->csdk_investor_amount);}
    	else{
    		$obj->csdk_investor_amount="";
    	}
    	if(!empty($result_lastest)){
    	$obj->lastest_evaluate=is_null(number_format($result_lastest->investor_after_evalute)) ? ""	:number_format($result_lastest->investor_after_evalute);
    	 

    	if (floatval($result_lastest->investor_payback)==0){
			$obj->lastest_investor_payback='-';
		}else{
			$obj->lastest_investor_payback=(string)(floatval($result_lastest->investor_payback));
		}
    	}
    	$obj->url_api_evalute=PDF_DOMAIN.'index.php?ctl=api&act=evalute&id='.$deal_id;
    	$obj->url_api_rate=PDF_DOMAIN.'index.php?ctl=api&act=rate&id='.$deal_id;
    	$obj->url_api_amount=PDF_DOMAIN.'index.php?ctl=api&act=amount&id='.$deal_id;
 	}
	else
	{
	$obj->r = "暂无数据";
	}
	//返回数据
	$obj->status = 200;
	CommonUtil::return_info($obj);	
	
?>