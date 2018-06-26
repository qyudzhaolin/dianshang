<?php
/**
* 成功案例
*/
class caseModule extends BaseModule
{
 	public function index()
	{	
		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		$deal = "id,img_deal_cover,deal_brief,com_time,capital,profession_info,vision_info,deal_sign,entry_info,solve_pain_info,operation_info";
		// 项目基本信息
		$dealcase = $GLOBALS ['db']->getRow ( "select {$deal} from " . DB_PREFIX . "deal where is_case = 1 and id={$id}" );
		// 融资记录
		$dealtrade = $GLOBALS ['db']->getAll ( " select id,period,investor_amount,investor_time from " . DB_PREFIX . "deal_trade_event   where deal_id=$id  ORDER BY investor_time asc,id asc" );
		// 项目亮点
		$dealpoint = $GLOBALS ['db']->getAll ( "select point_info from " . DB_PREFIX . "deal_sign_point where deal_id=$id order by create_time desc limit 6" );
		// 业务模式
		$operationsteps = $GLOBALS ['db']->getAll ( "select img_deal_opera_steps,opera_steps_name,opera_steps_brief from " . DB_PREFIX . "deal_operation_steps  where deal_id =$id  order by create_time desc limit 3 " );
		// 团队介绍
		$dealteam = $GLOBALS ['db']->getAll ( "select img_logo,name,title,brief  from " . DB_PREFIX . "deal_team  where deal_id =$id  order by id asc " );
		foreach ( $dealtrade as $k => $v ) {
			$event_id = $v ['id'];
        //融资机构
			$dealtrade[$k]['events'] = $GLOBALS ['db']->getAll ( " select s_name from " . DB_PREFIX . "deal_event_investor where event_id=$event_id  order by create_time desc limit 3 " );
			$dealtrade [$k] ['investor_time'] = to_date ( $v ['investor_time'], "Y年m月" );
		}
		//发展规划图
		$deal_data_img = $GLOBALS ['db']->getAll ( "select img_data_url from " . DB_PREFIX . "deal_data_img where deal_id =$id  order by create_time desc  " );  
		$dealcase ['com_time'] = to_date ( $dealcase ['com_time'], "Y年m月" );
		$GLOBALS ['tmpl']->assign ( "investorname", $investorname );
		$GLOBALS ['tmpl']->assign ( "vo", $dealcase );
		$GLOBALS ['tmpl']->assign ( "deal_data_img", $deal_data_img );
		$GLOBALS ['tmpl']->assign ( "dealtrade", $dealtrade );
		$GLOBALS ['tmpl']->assign ( "dealpoint", $dealpoint );
		$GLOBALS ['tmpl']->assign ( "operationsteps", $operationsteps );
		$GLOBALS ['tmpl']->assign ( "dealteam", $dealteam );
		$GLOBALS ['tmpl']->assign ("pageType",  PAGE_MENU_INVESTOR);
		$GLOBALS ['tmpl']->display( "casedetails.html" );
	}

	 
}

?>
