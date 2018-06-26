<?php
// +----------------------------------------------------------------------
// | 项目详情
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class dealdetailsModule extends BaseModule {
	
	public function index() {
		
		//	后台预览参数判断
		$preview = strim($_REQUEST['preview']);
		if ($preview) {
			$session_name='csdk'.md5('csdk');
			$adm_session = $_SESSION[$session_name];
			if(!isset($adm_session)){
				app_redirect(url("index#index"));
				return;
			}
			$preview_end = 'disabled="disabled"';
			$GLOBALS['tmpl']->assign("preview_end",$preview_end);
		}
		$userid = $GLOBALS ['user_info']['id'];;	# 登录信息-用户的ID
		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;	# 项目id
			
		// 项目基本信息
		$field = "id,img_deal_cover,deal_brief,period_id,province,city,view_count,user_id,profession_info,operation_info,mark_data_info,vision_info,financing_amount,pe_amount_plan,pe_least_amount,pe_sell_scale,is_case,is_effect,entry_info,deal_url";
		$deal = $GLOBALS ['db']->getRow ( "select $field from " . DB_PREFIX . "deal where id = {$id} and is_delete = 0" );
		
		if(empty($deal)){
			app_redirect(url('deals#index'));
		}
		if(stristr($deal ['deal_url'],'http://')){
		    $deal ['deal_url_http']   = $deal ['deal_url'];
		    $deal ['deal_url']        = substr($deal ['deal_url'],7);
		}else{
		    $deal ['deal_url_http']   = 'http://'.$deal ['deal_url'];
		    $deal ['deal_url']        = $deal ['deal_url'];
		}
		$deal ['province'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id={$deal ['province']} and region_level=2");
// 		$deal ['city'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id={$deal ['city']} and region_level=3");
		
		// 浏览日志
		if(!$preview && $userid){
			$focus_log = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal_visit_log  where user_id = ".$userid." and deal_id=".$id."");
			if (empty($focus_log) && $deal['user_id'] != $user_id){
				$client_ip = get_client_ip();
				$time = time();
				$GLOBALS['db']->query("insert into ".DB_PREFIX."deal_visit_log (user_id,deal_id,client_ip,create_time) values (".$userid.",".$id.",'".$client_ip."',".$time.")");
			}
		}
		// 增加浏览次数
		$GLOBALS['db']->query("update ".DB_PREFIX."deal set view_count = view_count+1 where id=".$id." "); 

		// 项目标签
		$deal_cate = $GLOBALS ['db']->getAll ( "select cate.name from " . DB_PREFIX . "deal project, " . DB_PREFIX . "deal_cate cate, " . DB_PREFIX . "deal_select_cate selected where selected.deal_id = project.id and selected.cate_id = cate.id and project.id = " . $id . " " );
		
		// 商业模式图列表 deal_operation_steps表
		$dealoperationsteps = $GLOBALS ['db']->getAll ( "select img_deal_opera_steps,opera_steps_name,opera_steps_brief from " . DB_PREFIX . "deal_operation_steps  where deal_id ={$id}  order by create_time desc limit 3 " );
		
		// 团队介绍
		$dealteam = $GLOBALS ['db']->getAll ( "select img_logo,name,title,brief  from " . DB_PREFIX . "deal_team  where deal_id =$id  order by id asc limit 3 " );
		
		$in_access = 0;
		//$in_intend = $userid ? 0 : 99;	# 未登录 隐藏按钮
		//$in_intend = $deal['is_effect'] == 3 ? 99 : $in_intend;	# 已投 隐藏按钮

		// 经典案例 与用户各种状态无关 所有信息都可查看
		if($deal['is_case'] == 1 && $deal['is_effect'] == 3 ){
			$this->getAccessInfo($deal);
			$in_access = 1;
		}else{
    		// 非经典案例 需要判断用户登录及认证状态
			if($userid){
				// 用户是否审核 0:未审核 1:审核
				$isreview = $GLOBALS ['db']->getOne ( "select is_review from " . DB_PREFIX . "user  where id = " . $userid . " " );
				$GLOBALS ['tmpl']->assign ( "is_review", $isreview );
			
				if($isreview == 1){
					//  可查看发展现状、发展规划、融资计划
					$this->getAccessInfo($deal);
					$in_access = 1;
				}
            }
		}
	    // 用户是否发送过投资意向 状态判断
		if($userid){
		    $intend_log = $GLOBALS ['db']->getRow ( "select id from " . DB_PREFIX . "deal_intend_log  where user_id = " . $userid . " and deal_id=" . $id . "" );
		    $intend_log && $in_intend = 1;
		}
		
		// 项目融资阶段，已投项目不展示
		if($deal['is_effect'] != 3){
    		$deal_period = $GLOBALS ['db']->getOne ( "select name from " . DB_PREFIX . "deal_period where id = {$deal['period_id']} " );
    		$GLOBALS ['tmpl']->assign ( "deal_period", $deal_period );
		}
		
		$GLOBALS ['tmpl']->assign ( "deal", $deal );
		$GLOBALS ['tmpl']->assign ( "deal_cate", $deal_cate );
		$GLOBALS ['tmpl']->assign ( "dealoperationsteps", $dealoperationsteps );
		$GLOBALS ['tmpl']->assign ( "dealteam", $dealteam );
		$GLOBALS ['tmpl']->assign ( "in_access", $in_access );
		$GLOBALS ['tmpl']->assign ( "in_intend", $in_intend );
		$GLOBALS ['tmpl']->assign ( "pageType", PAGE_MENU_DEAL );
		$GLOBALS ['tmpl']->display ( "dealdetails.html" );
	}
	
	private function getAccessInfo($deal){
		
		// 发展现状 成绩数据简介（deal.mark_data_info） 数据图片（deal_data_img表）
		$deal_data_img = $GLOBALS ['db']->getAll ( "select img_data_url from " . DB_PREFIX . "deal_data_img where deal_id ={$deal['id']} order by create_time desc  " );
		$GLOBALS ['tmpl']->assign ( "deal_data_img", $deal_data_img );
		
		// 发展规划 deal.vision_info
		
		// 融资计划，已投项目不展示
		if($deal['is_effect'] != 3){

		    // 融资后估值
		    $pe_evaluate = $deal['pe_amount_plan'] / $deal['pe_sell_scale'] * 100;
// 		    $len = strpos ( $pe_evaluate, "." );
// 		    if($len){
// 		        $pe_evaluate = substr ( $pe_evaluate, 0, $len );
// 		    }
			
			// 融资进度条
			$progress_bar = ($deal['financing_amount'] / $deal['pe_amount_plan']) * 100;
		
		    $GLOBALS ['tmpl']->assign ( "pe_evaluate", $pe_evaluate ? number_format($pe_evaluate) : '-' );
			$GLOBALS ['tmpl']->assign ( "progress_bar", $progress_bar );
		}
	}
	
	/*
	 * 添加投资意向接口
	 * @param int $id 项目ID
	 * @param int $uid 用户ID（无需传参，session获取）
	 */
	public function add_invest() {
		$uid = $GLOBALS ['user_info']['id'];
		if($uid){
			// 用户是否审核 0:未审核 1:审核
			$isreview = $GLOBALS ['db']->getOne ( "select is_review from " . DB_PREFIX . "user  where id = " . $uid . " " );
			if($isreview == 1){
				$project_id = isset ( $_POST ['id'] ) ? intval ( $_POST ['id'] ) : 0;
				if (! is_null ( $project_id )) {
					 
						$time = time ();
						$in_intend = $GLOBALS ['db']->getRow ( "select id from " . DB_PREFIX . "deal_intend_log  where user_id = " . $uid . " and deal_id=" . $project_id . "" );
						if (empty ( $in_intend )) {
							$insert_result = $GLOBALS ['db']->query ( "insert into " . DB_PREFIX . "deal_intend_log (deal_id,user_id,create_time) values (" . $project_id . "," . $uid . "," . $time . ")" );
							$update_deal = $GLOBALS ['db']->query ( "update " . DB_PREFIX . "user set intend_count = intend_count+1 where id=" . $uid . " " );
							if ($insert_result && $update_deal) {
								$res ['status'] = 1;
								$res ['info'] = "感谢您对该项目的支持，磁斯达克服务人员会在两个工作日内联系您核实投资意向。您也可拨打磁斯达克客服电话：400-862-8262咨询投资详情。";
							} else {
								$res ['status'] = 2;
								$res ['info'] = "发送投资意向失败";
							}
						}else{
							$res ['status'] = 3;
							$res ['info'] = "已发送过投资意向";
						}
					 
				} else {
					$res ['status'] = 4;
					$res ['info'] = "参数丢失";
				}	 
			}else{
				$res ['status'] = 0;
				$res ['info'] = "尊敬的投资人，认证后才可发送投资意向！";
			}
		}else{
			$res ['status'] = 99;
			$res ['info'] = "没有登录";
			$res ['data'] = url("user#login");
		}
		ajax_return ( $res );
	}
}
?>