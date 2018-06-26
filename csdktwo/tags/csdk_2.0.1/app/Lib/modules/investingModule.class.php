<?php
// +----------------------------------------------------------------------
// | 个人中心-意向项目
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class investingModule extends BaseModule{
	
	private $rows = 5; # 意向项目列表显示行数
	
	/*
	 * 个人中心-意向项目
	 */
	public function index(){
		$user_id = $GLOBALS['user_info']['id'];
		
		// 获取我的投资意向项目列表
		$field 	= "log.deal_id,log.user_id,deal.name,deal.s_name,deal.img_deal_logo,deal.deal_sign,deal.comment_count,deal.province,deal.period_id";
		
		$sql 	= "select {$field} from ".DB_PREFIX."deal_intend_log as log,".DB_PREFIX."deal as deal where log.user_id={$user_id} and log.deal_id=deal.id and deal.is_effect in (2,3) order by log.create_time desc limit {$this->rows}";
		$invest = $GLOBALS['db']->getAll($sql);
		
		if($invest){
			$invest_more 	= count($invest) < $this->rows ? 0 : 1;	#加载更多按钮显示隐藏标识位
			$period 		= $this->getPeriod();
			$city 			= $this->getCity();
			
			foreach($invest as &$v){
				$v['province'] 	= $city[$v['province']];
				$v['period_id'] = $period[$v['period_id']]['name'];
				$v['focus'] 	= $GLOBALS ['db']->getOne("select id from ".DB_PREFIX."deal_focus_log where user_id={$user_id} and deal_id={$v['deal_id']}");
			}
			unset($v);
		}
		
		$msgNum = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."user_notify where user_id = {$user_id} and is_read=0");
		$GLOBALS['tmpl']->assign("msgNum",$msgNum);
// 		var_dump($invest);
		$GLOBALS['tmpl']->assign("invest",$invest);
		$GLOBALS['tmpl']->assign("invest_more",$invest_more);
		$GLOBALS['tmpl']->assign("period",$period);
		$GLOBALS['tmpl']->assign("city",$city);
		
		$GLOBALS['tmpl']->assign("page_title","我要投资项目");
		$GLOBALS['tmpl']->assign("pageType",PAGE_MENU_USER_CENTER);
		$GLOBALS['tmpl']->assign("sideType",SIDE_MENU_INVESTING);
		$GLOBALS['tmpl']->display("investing.html");
	}
	
	/*
	 * 个人中心-意向项目（加载更多接口）
	 * @param int $counter 		项目条数计数器
	*/
	public function more(){
		$user_id = $GLOBALS['user_info']['id'];
		$res = array('status'=>99,'info'=>0,'data'=>''); //用于返回的数据
		
		if($user_id){
			$counter =  isset($_POST['counter'])? intval($_POST['counter']) : 0;
			
			// 获取我的投资意向项目列表
			$field 	= "log.deal_id,log.user_id,deal.name,deal.s_name,deal.img_deal_logo,deal.deal_sign,deal.comment_count,deal.province,deal.period_id";
			
			$sql 	= "select {$field} from ".DB_PREFIX."deal_intend_log as log,".DB_PREFIX."deal as deal where log.user_id={$user_id} and log.deal_id=deal.id and deal.is_effect in (2,3) order by log.create_time desc limit {$counter},{$this->rows}";
			$invest = $GLOBALS['db']->getAll($sql);
			
			if($invest){
				$period 		= $this->getPeriod();
				$city 			= $this->getCity();
				foreach($invest as &$v){
					$v['img_deal_logo'] 	= getQiniuPath($v['img_deal_logo'],'img').'?imageView2/1/w/100/h/100';
// 					$v['deal_sign'] 		= msubstr($v['deal_sign'],0,57);
					$v['url'] 		= url("dealdetails#index",array('id'=>$v['deal_id']));
					$v['province'] 	= $city[$v['province']];
					$v['period_id'] = $period[$v['period_id']]['name'];
					$v['focus'] 	= $GLOBALS ['db']->getOne("select id from ".DB_PREFIX."deal_focus_log where user_id={$user_id} and deal_id={$v['deal_id']}");
				}
				unset($v);
				$res['info'] = count($invest) < $this->rows ? 0 : 1;	#加载更多按钮显示隐藏标识位
				$res['data'] = $invest;
			}else{
				$res['status'] 	= 1;
				$res['info'] 	= "没有数据";
			}
		}else{
			$res['status'] 	= 0;
			$res['info'] 	= "Error:用户丢失";
		}
		ajax_return($res);
	}
	
	// 获取融资阶段标识列表
	private function getPeriod(){
		$period_ = $GLOBALS['db']->getAll("select id,name,mapname from ".DB_PREFIX."deal_period");
		foreach ($period_ as $val){
			$period[$val['id']] = array('name'=>$val['name'],'mapname'=>$val['mapname']);
		}
		return $period;
	}
	
	// 获取城市标识列表
	private function getCity(){
		$city_ = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."region_conf where region_level=2");
		foreach ($city_ as $val){
			$city[$val['id']] = $val['name'];
		}
		return $city;
	}
}
?>