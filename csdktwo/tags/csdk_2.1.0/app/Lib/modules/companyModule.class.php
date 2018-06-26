<?php
class companyModule extends BaseModule {
	public function index() {
		// 基金管理公司的id
		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		$user_id = $GLOBALS['user_info']['id'];
		if ($id == 0) {
			// 404
			app_redirect ( url ( "error#index" ) );
		}
		// 工商注册信息
		$managersfield = "id,short_name,legal_person,com_time,reg_found,registration_address";
		$managersinfo = $GLOBALS ['db']->getRow ( "select {$managersfield} from " . DB_PREFIX . "fund_managers where id = {$id} " );
		$managersinfo ['com_time'] = to_date ( $managersinfo ['com_time'], "Y.m.d" );
		 $managersinfo['reg_found'] = number_format($managersinfo['reg_found'],2);
		// 旗下管理基金
		$fundfield = "id,short_name,name,managers_id,total_amount,establish_date";
		$fundinfo = $GLOBALS ['db']->getAll ( "select {$fundfield} from " . DB_PREFIX . "fund where managers_id = {$id} and status=2 and is_csdk_fund=1 and is_delete=1 order by establish_date desc" );
		foreach ( $fundinfo as $k => $v ) {
			$fund_url=$GLOBALS ['db']->getAll ( "select id from " . DB_PREFIX . "user_fund_relation where fund_id = {$v['id']}     and user_id={$user_id} " );
			if(!empty($fund_url)){
			$fundinfo[$k]['fund_url'] = 1;
			}
			else 
			{
			$fundinfo[$k]['fund_url'] = 0;
			}	
				
		}
		// 管理团队
		$field = "id,user_id,user_logo,title,managers_id,is_del";
		$info = $GLOBALS ['db']->getAll ( "select {$field} from " . DB_PREFIX . "fund_managers_team where managers_id = {$id} and is_del = 1" );
		// 查找会员姓名
		$name = $GLOBALS ['db']->getAll ( "select id,user_name from " . DB_PREFIX . "user where is_effect=1 and is_review=1 " );
		foreach ( $name as $k => $v ) {
			$nameMap [$v ['id']] = $v ['user_name'];
		}
		foreach ( $info as $k => $v ) {
			$info [$k] ['name'] = $nameMap [$v ['user_id']];
		}
		$GLOBALS ['tmpl']->assign ( "list", $info );
		$GLOBALS ['tmpl']->assign("sideType",SIDE_MENU_INVESTED);
		$GLOBALS ['tmpl']->assign ( "fundinfo", $fundinfo );
		$GLOBALS ['tmpl']->assign ( "managers", $managersinfo );
		$GLOBALS ['tmpl']->display ( "investors_company.html" );
	}
}
?>