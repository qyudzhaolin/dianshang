<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:zhaoLin(97139915@qq.com)
// +----------------------------------------------------------------------
class investorsModule extends BaseModule {
	/*
	 * 我是投资人 @param int $id 用户ID @author zhaoLin
	 */
	public function index() {
		$field = "id,b_url,b_pc_img";
		$deal = "id,s_name,deal_url,img_deal_logo,deal_sign";
		$dealbest = "id,name,deal_url,img_deal_logo,province,period_id,deal_sign,comment_count";
		$carousel = $GLOBALS ['db']->getAll ( "select {$field} from " . DB_PREFIX . "banner where  b_channel in(2,3) and  b_bygroup = 2 and b_publish_state = 2 order by b_sort desc limit 6" );
	   foreach($carousel as &$v){
		    $v['b_url'] = $v['b_url'] ? (stristr($v['b_url'],'http://') ? $v['b_url'] : 'http://'.$v['b_url']) : '';
		    $v['b_pc_img'] = getQiniuPath($v['b_pc_img'],'img')."?imageView2/1/w/1000/h/360";
		}
		unset($v);
		
		// 成功案列显示
		$dealcase = $GLOBALS ['db']->getAll ( "select {$deal} from " . DB_PREFIX . "deal where is_case = 1 and is_effect=3 and is_delete=0 order by sort asc,id desc,create_time desc limit 3" );
		// 精品项目
		$dealbest = $GLOBALS ['db']->getAll ( "select {$dealbest} from " . DB_PREFIX . "deal where is_best = 1 and is_effect=2 and is_delete=0 order by sort asc,id desc,create_time desc limit 4" );
		// 项目融资阶段
		$period = $GLOBALS ['db']->getAll ( "select id,name from " . DB_PREFIX . "deal_period " );
		// 省级显示
		$province = $GLOBALS ['db']->getAll ( "select id,name from " . DB_PREFIX . "region_conf where region_level=2 " );
		foreach ( $period as $k => $v ) {
			$periodMap [$v ['id']] = $v ['name'];
		}
		foreach ( $province as $k => $v ) {
			$provinceMap [$v ['id']] = $v ['name'];
		}
		foreach ( $dealbest as $k => $v ) {
			$dealbest [$k] ['period_id'] = $periodMap [$v ['period_id']];
			$dealbest [$k] ['province'] = $provinceMap [$v ['province']];
			$deal_id = $v ['id'];
         
           // 获得当前登录用户的ID
        $user_info= $GLOBALS['user_info'] ;
        $user_id=$user_info['id'];
        if($user_id){
	       $user_result = $GLOBALS ['db']->getOne ( "select id from " . DB_PREFIX . "deal_focus_log where user_id=$user_id  and deal_id=$deal_id" );
	        //关注状态判断 
			if ($user_result) {
				$dealbest [$k] ['state'] = 1;
			} else {
				$dealbest [$k] ['state'] = 2;
	 
        }
	

		}

		
}
	
		$GLOBALS ['tmpl']->assign ( "carousel", $carousel );
		$GLOBALS ['tmpl']->assign ( "dealcase", $dealcase );
		$GLOBALS ['tmpl']->assign ( "dealbest", $dealbest );
		$GLOBALS ['tmpl']->assign ("pageType",  PAGE_MENU_INVESTOR);
	
		$GLOBALS ['tmpl']->display ( "investorindex.html" );
	}

	/*
	 * 项目添加关注
	 * 公共方法，修改需谨慎
	 * @param int id 项目ID
	 */
	public function attention() {
		$id = isset ( $_POST ['id'] ) ? intval ( $_POST ['id'] ) : 0;
		if($id){
			$res = array ('status' => 0,'info' => '','data' => '');
			// 查询当前用户是否关注过此项目
			$user_info = $GLOBALS['user_info'] ;
			if($user_info){
				$user_id = $user_info['id'];
				$result = $GLOBALS ['db']->getAll ( "select id,deal_id,user_id from " . DB_PREFIX . "deal_focus_log where user_id={$user_id} and deal_id={$id}" );
				if ($result) {
					$res['status'] 	= 0;
					$res['info'] 	= "Error:已关注过";
				} else {
					$time = time ();
					// 增加关注记录
					$deallog = $GLOBALS ['db']->query ( "iNSERT INTO " . DB_PREFIX . "deal_focus_log VALUES ('',{$id},{$user_id},{$time})" );
					// 更新关注统计字段
					$result = $GLOBALS ['db']->query ( "update " . DB_PREFIX . "deal set comment_count=comment_count+1, focus_count=focus_count+1 where id={$id}" );
					$res['status'] 	= 100;
					$res['info'] 	= "关注成功";
				}
			}else{
				$res['status'] 	= 0;
				$res['info'] 	= "对不起,添加关注请您先登陆";
			}
		}else{
			$res['status'] 	= 0;
			$res['info'] 	= "Error:参数丢失";
		}

		ajax_return ( $res );
	}
 
}
?>