<?php
/**
* 成功案例
*/
class caseModule extends BaseModule
{
 	public function index()
	{	
		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		$deal = "id,img_deal_cover,deal_brief,period_id,province,city,view_count,user_id,profession_info,operation_info,vision_info,is_case,is_effect,entry_info,deal_url,vision_pc_img,profession_pc_img,spot_pc_img,com_time,capital";
		// 项目基本信息
		$dealcase = $GLOBALS ['db']->getRow ( "select {$deal} from " . DB_PREFIX . "deal where is_case = 1 and id={$id}" );
		// 项目亮点
		$dealpoint = $GLOBALS ['db']->getAll ( "select point_info from " . DB_PREFIX . "deal_sign_point where deal_id=$id order by create_time desc limit 6" );
		// 业务模式
		$operationsteps = $GLOBALS ['db']->getAll ( "select img_deal_opera_steps,opera_steps_name,opera_steps_brief from " . DB_PREFIX . "deal_operation_steps  where deal_id =$id  order by create_time desc limit 3 " );
		// 团队介绍
		$dealteam = $GLOBALS ['db']->getAll ( "select img_logo,name,title,brief  from " . DB_PREFIX . "deal_team  where deal_id =$id  order by id asc " );
	    //轮次
		$period = $GLOBALS ['db']->getAll ( "select id,name from " . DB_PREFIX . "deal_period " );
		foreach ( $period as $k => $v ) {
			$periodMap [$v ['id']] = $v ['name'];
		}
		//融资金额
		$investor_amount = $GLOBALS ['db']->getAll (" 
				SELECT event.id,period,investor_time ,sum(investor_amount) as investor_amount
FROM cixi_deal_trade_event AS event left join  cixi_deal_trade_fund_relation AS fund on (event.id=fund.deal_trade_event_id)
		WHERE `event`.deal_id=$id  and investor_record_type=2   and `event`.deal_id=fund.deal_id
group by event.id
ORDER BY investor_time asc,id asc" ); 
        foreach ( $investor_amount as $k => $v ) {
			$event_id = $v ['id'];
        //融资机构
			$investor_amount[$k]['events'] = $GLOBALS ['db']->getAll ( "select short_name from " . DB_PREFIX . "deal_trade_fund_relation as  relation," . DB_PREFIX . "fund as fund where fund.id=relation.fund_id and relation.deal_id=$id and relation.deal_trade_event_id=$event_id order by create_time desc  ,relation.id desc  limit 3  " ); 
			$investor_amount [$k] ['investor_time'] = to_date ( $v ['investor_time'], "Y年m月" );
			$investor_amount [$k] ['period_id'] = $periodMap [$v ['period']];
		}
		//发展现状图
		$deal_data_img = $GLOBALS ['db']->getAll( "select img_data_url from " . DB_PREFIX . "deal_data_img where deal_id =$id  order by create_time desc  " );  
		$dealcase ['com_time'] = to_date ( $dealcase ['com_time'], "Y年m月" );
		 //项目亮点的权限控制
       if(empty($dealcase['spot_pc_img'])){
    	$GLOBALS ['tmpl']->assign ( "spot_pc_img", $spot_pc_img=1);
		}
       if(empty($dealcase['spot_pc_img'])&& empty($dealcase['entry_info'])){
    	$GLOBALS ['tmpl']->assign ( "spot_pc_img", $spot_pc_img=3);
		}
        //行业调查
      if(empty($dealcase['profession_pc_img'])){
    	$GLOBALS ['tmpl']->assign ( "profession_pc_img", $profession_pc_img=1);
		}
       if(empty($dealcase['profession_info'])&& empty($dealcase['profession_pc_img'])){
    	$GLOBALS ['tmpl']->assign ( "profession_pc_img", $profession_pc_img=3);
		}
        //发展规划
        if(empty($dealcase['vision_pc_img'])){
    	$GLOBALS ['tmpl']->assign ( "vision_pc_img", $vision_pc_img=1);
		}
       if(empty($dealcase['vision_info'])&& empty($dealcase['vision_pc_img'])){
    	$GLOBALS ['tmpl']->assign ( "vision_pc_img", $vision_pc_img=3);
		}
		$GLOBALS ['tmpl']->assign ( "investorname", $investorname );
		$GLOBALS ['tmpl']->assign ( "vo", $dealcase );
		$GLOBALS ['tmpl']->assign ( "deal_data_img", $deal_data_img );
		$GLOBALS ['tmpl']->assign ( "dealtrade", $investor_amount );
		$GLOBALS ['tmpl']->assign ( "dealpoint", $dealpoint );
		$GLOBALS ['tmpl']->assign ( "operationsteps", $operationsteps );
		$GLOBALS ['tmpl']->assign ( "dealteam", $dealteam );
		$GLOBALS ['tmpl']->assign ("pageType",  PAGE_MENU_INVESTOR);
		$GLOBALS ['tmpl']->display( "casedetails.html" );
	}

	 
}

?>
