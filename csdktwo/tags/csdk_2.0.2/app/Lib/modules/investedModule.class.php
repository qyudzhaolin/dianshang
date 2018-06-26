<?php
// +----------------------------------------------------------------------
// | 个人中心-我的投资相关
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class investedModule extends BaseModule{
	
	private $invested_rows = 10; # 我的投资-我购买的基金列表显示行数
	private $invest_deal_rows = 10; # 我的投资-基金投资项目列表显示行数
	
	/*
	 * 个人中心-我的投资
	 */
	public function index(){
		$user_id = $GLOBALS['user_info']['id'];
		$home_is_review =  $GLOBALS['db']->getOne("select is_review from ".DB_PREFIX."user where id = {$user_id}");
		// 获取我购买的基金及信息
		$field 	= "r.user_id,r.fund_id,r.investor_amount,r.investor_rate,r.is_default_fund,f.code,f.name,f.short_name,f.total_amount,f.establish_date";
		$map 	= "r.user_id={$user_id} and r.fund_id=f.id and f.status=2 and f.is_delete=1";
		
		$sql 	= "select {$field} from ".DB_PREFIX."user_fund_relation as r,".DB_PREFIX."fund as f";
		$sql 	.= " where {$map} order by f.establish_date desc,f.id desc limit {$this->invested_rows}";
		
		$my_invested          = $GLOBALS['db']->getAll($sql);
		$my_invested_count    = $GLOBALS['db']->getOne("select count(1) from ".DB_PREFIX."user_fund_relation as r,".DB_PREFIX."fund as f where {$map}");
		$my_invested_more     = count($my_invested) < $my_invested_count ? 1 : 0;	  # 加载更多按钮显示(1)隐藏(0)标识位
		
		$msgNum = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."user_notify where user_id = {$user_id} and is_read=0");
		$GLOBALS['tmpl']->assign("msgNum",$msgNum);
		$GLOBALS['tmpl']->assign("home_is_review",$home_is_review);
		$GLOBALS['tmpl']->assign("my_invested",$my_invested);
		$GLOBALS['tmpl']->assign("my_invested_more",$my_invested_more);
		$GLOBALS['tmpl']->assign("page_title","我的投资");
		$GLOBALS ['tmpl']->assign("pageType",PAGE_MENU_USER_CENTER);
		$GLOBALS ['tmpl']->assign("sideType",SIDE_MENU_INVESTED);
		$GLOBALS['tmpl']->display("invested_index.html");
	}
	
	/*
	 * 个人中心-我的投资（加载更多及搜索接口）
	 * @param int $counter 		基金条数计数器
	 * @param string $keyword 	搜索关键字
	*/
	public function my_invested_more(){
		$user_id = $GLOBALS['user_info']['id'];
		$res = array('status'=>99,'info'=>0,'data'=>'','more'=>''); //用于返回的数据
		
		if($user_id){
			$counter =  isset($_POST['counter'])? intval($_POST['counter']) : 0;
			$keyword =  isset($_POST['keyword'])? strim($_POST['keyword']) : '';
			
			$field 	= "r.user_id,r.fund_id,r.investor_amount,r.investor_rate,r.is_default_fund,f.code,f.name,f.short_name,f.total_amount,f.establish_date";
			$sql 	= "select {$field} from ".DB_PREFIX."user_fund_relation as r,".DB_PREFIX."fund as f where ";
			$map    = " r.user_id={$user_id} and r.fund_id=f.id";
			if($keyword){
				$map .= " and f.name like '%{$keyword}%'";
			}
			$map     .= " and f.status=2 and f.is_delete=1";
			$sql     = $sql.$map." order by f.establish_date desc,f.id desc limit {$counter},{$this->invested_rows}";
			$lists   = $GLOBALS['db']->getAll($sql);
			if (empty($lists)){
				$res['status'] 	= 1;
				$res['info'] 	= "没有数据";
			}else{
				foreach ($lists as $k=>$v) {
					$lists[$k]['total_amount']     = number_format($lists[$k]['total_amount']);
					$lists[$k]['investor_amount']  = number_format($lists[$k]['investor_amount']);
					$lists[$k]['establish_date']   = to_date($lists[$k]['establish_date'],"Y.m.d");
					$lists[$k]['fund_url']         = url("invested#invested_fund",array('id'=>$v['fund_id']));
				}
				
				$lists_count = $GLOBALS['db']->getOne("select count(1) from ".DB_PREFIX."user_fund_relation as r,".DB_PREFIX."fund as f where {$map}");
				$res['more'] = count($lists) + $counter < $lists_count ? 1 : 0;
				$res['data'] = $lists;
			}
		}else{
			$res['status'] 	= 0;
			$res['info'] 	= "Error:用户丢失";
		}
		ajax_return($res);
	}
	
	/*
	 * 个人中心-我的投资-基金详情页
	 * @parm int $id 基金ID
	 */
	public function invested_fund(){
		$id =  isset($_GET['id'])? intval($_GET['id']) : 0;
		if ($id == 0) {
			// 404
			app_redirect(url("error#index"));
		}
		
		$user_id = $GLOBALS['user_info']['id'];
		
		// 基金基本信息
		$invested = $GLOBALS['db']->getRow("select id,name,short_name,code,manager,total_amount,establish_date,deadline,summary,max_payback,average_payback,total_payback,status from ".DB_PREFIX."fund where id={$id}");
	
		// 基金已投资金额
		$amount_total = $GLOBALS['db']->getOne("select  sum(investor_amount) as sum_amount   
from cixi_deal as deal left join cixi_deal_trade_fund_relation as relation on deal.id=relation.deal_id,cixi_deal_trade_event as event 
where    deal.is_publish=2 and deal.is_effect in (2,3)  and fund_id={$id} and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id and investor_record_type=2 ");
		$invested['amount_total'] = $amount_total ? $amount_total : 0;
		
		// 我的投资信息
		$invested['my_invested'] = $GLOBALS['db']->getRow("select investor_amount,investor_rate from ".DB_PREFIX."user_fund_relation where fund_id={$id} and user_id={$user_id}");
		
		// 核心管理团队
		$manage_team = $GLOBALS['db']->getAll("select name,head_logo,position,summary from ".DB_PREFIX."fund_manage_team where fund_id={$id} order by id asc limit 5");
		
		// 基金投资项目列表
	 
		$sql ="
				select event.deal_id,relation.fund_id,sum(investor_amount) as investor_amount,Max(investor_date) as investor_date,deal.s_name,deal.name 
from ".DB_PREFIX."deal as deal left join ".DB_PREFIX."deal_trade_fund_relation as relation on deal.id=relation.deal_id,".DB_PREFIX."deal_trade_event as event 
where    deal.is_publish=2 and deal.is_effect in (2,3)  and fund_id={$id} and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id and investor_record_type=2
GROUP BY event.deal_id order by investor_amount desc ,investor_date desc ,relation.id desc limit {$this->invest_deal_rows}";
		$deal = $GLOBALS['db']->getAll($sql);
		$my_deal_more = 0;
		if($deal){
			foreach($deal as $k=>$v){
				//$deal[$k]['valuations']         = number_format(($v['pe_amount_plan']/$v['pe_sell_scale'])*100);
				$deal[$k]['valuations']         =  number_format($GLOBALS['db']->getOne("select investor_after_evalute from ".DB_PREFIX."deal_trade_event where deal_id={$v['deal_id']} and investor_record_type=2 order by investor_time desc limit 1 "));
				$deal[$k]['investor_amount']    = number_format($v['investor_amount']);
				$deal[$k]['investor_date']      = to_date($v['investor_date'],'Y.m.d');
			}
			//$my_deal_count   = $GLOBALS['db']->getOne("select count(1) from ".DB_PREFIX."fund_deal_relation as r,".DB_PREFIX."deal as d where {$map}");
			$my_deal_more    = count($deal) < $my_deal_count ? 1 : 0;	  # 加载更多按钮显示(1)隐藏(0)标识位
		} 
		$msgNum = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."user_notify where user_id = {$user_id} and is_read=0");
		$GLOBALS['tmpl']->assign("msgNum",$msgNum);
		$GLOBALS['tmpl']->assign("invested",$invested);
		$GLOBALS['tmpl']->assign("manage_team",$manage_team);
		$GLOBALS['tmpl']->assign("deal",$deal);
		$GLOBALS['tmpl']->assign("my_deal_more",$my_deal_more);
		$GLOBALS['tmpl']->assign("page_title","基金详情");
		$GLOBALS ['tmpl']->assign("pageType",PAGE_MENU_USER_CENTER);
		$GLOBALS ['tmpl']->assign("sideType",SIDE_MENU_INVESTED);
		$GLOBALS['tmpl']->display("invested_fund.html");
	}
	
	/*
	 * 个人中心-我的投资-基金详情页（加载更多接口）
	* @param int $id 		基金ID
	* @param int $counter 	基金条数计数器
	*/	
	public function invested_fund_more(){
		
		$id =  isset($_POST['id'])? intval($_POST['id']) : 0;
		
		if($id){
			$counter =  isset($_POST['counter'])? intval($_POST['counter']) : 0;
			$res = array('status'=>99,'info'=>0,'data'=>'','more'=>''); //用于返回的数据
				
			$field   = "r.fund_id,r.deal_id,r.investor_amount,r.investor_date,d.name,d.s_name,d.pe_amount_plan,d.pe_sell_scale";
			$sql     = "select {$field} from ".DB_PREFIX."fund_deal_relation as r,".DB_PREFIX."deal as d where ";
			$map     = " r.fund_id={$id} and r.deal_id=d.id and d.is_effect in (2,3)";
			$sql     = $sql . $map . " order by r.investor_amount desc,r.id limit {$counter},{$this->invest_deal_rows}";
			
			$lists = $GLOBALS['db']->getAll($sql);
			if (!empty($lists)){
				foreach ($lists as $k=>$v) {
					$lists[$k]['valuations']       = number_format(($v['pe_amount_plan']/$v['pe_sell_scale'])*100);
					$lists[$k]['investor_amount']  = number_format($v['investor_amount']);
					$lists[$k]['investor_date']    = to_date($v['investor_date'],'Y.m.d');
					$lists[$k]['fund_url']         = url("invested#deal_details",array('fund_id'=>$v['fund_id'],'deal_id'=>$v['deal_id']));
				}
				$lists_count = $GLOBALS['db']->getOne("select count(1) from ".DB_PREFIX."fund_deal_relation as r,".DB_PREFIX."deal as d where {$map}");
				$res['more'] = count($lists) + $counter < $lists_count ? 1 : 0;
				$res['data'] = $lists;
			}
		}else{
			$res['status'] 	= 0;
			$res['info'] 	= "Error:参数丢失";
		}
		
		ajax_return($res);
	}
	
	/*
	 * 个人中心-我的投资-项目详情页
	* @param int $fund_id 		基金ID
	* @param int $deal_id 		项目ID
	*/
	public function deal_details(){
// 		$fund_id =  isset($_GET['fund_id'])? intval($_GET['fund_id']) : 0;
// 		$deal_id =  isset($_GET['deal_id'])? intval($_GET['deal_id']) : 0;
		$fund_id =  isset($_GET['id'])? intval($_GET['id']) : 0;
		$deal_id =  isset($_GET['id2'])? intval($_GET['id2']) : 0;
		if ($fund_id == 0 || $deal_id == 0) {
			// 404
			app_redirect(url("error#index"));
		}
		
		// 基金信息
		$invest_info = $GLOBALS['db']->getRow("select id,name,short_name from ".DB_PREFIX."fund where id={$fund_id}");
		
		// 项目信息
		$field = "id,name,s_name,company_name,com_addr,com_busi,com_legal,com_tel,com_web,com_reg_found,com_time,out_plan,entry_info";
		$deal_info = $GLOBALS['db']->getRow("select {$field} from ".DB_PREFIX."deal where id={$deal_id}  and is_publish=2");
		
		// 查询磁斯达克最新融资信息->获取 投资前估值,投资后估值,该轮融资总额 等数据
		$info_csdk = $GLOBALS['db']->getRow(" 
 		SELECT event.id,period,investor_before_evalute,investor_after_evalute ,investor_amount  as csdk_investor_amount  
FROM ".DB_PREFIX."deal_trade_event AS event,".DB_PREFIX."deal_trade_fund_relation as fund
WHERE 
`event`.deal_id={$deal_id} and investor_record_type=2 and is_csdk_fund=1 and deal_trade_event_id=`event`.id and fund_id={$fund_id}

ORDER BY investor_time desc,`event`.id DESC LIMIT 0,1");
		
		if($info_csdk){
		    $info_csdk['investor_before_evalute'] = number_format($info_csdk['investor_before_evalute']);
		    $info_csdk['investor_after_evalute']  = number_format($info_csdk['investor_after_evalute']);
		    $info_csdk['csdk_investor_amount']         = number_format($info_csdk['csdk_investor_amount']);
		    
    		//查询磁斯达克最新融资投资金额（当前基金+当前项目+CSDK参与下的最新融资 => 再查询投资记录，拿出投资总额）
    		$enent_id = $info_csdk['period'];
 
    		$info_csdk['investor_total_amount']    = number_format($GLOBALS['db']->getOne(" 
    		select sum(investor_amount)    from ".DB_PREFIX."deal_trade_fund_relation as fund , ".DB_PREFIX."deal_trade_event as deal where  period={$enent_id} and     investor_record_type=2 and deal.deal_id={$deal_id} and fund.deal_id=deal.deal_id and fund.deal_trade_event_id=deal.id"));
    		$info_csdk['period']=$GLOBALS['db']->getOne("select name from cixi_deal_period where id='{$info_csdk['period']}'") ;
		}else{
		    $info_csdk['investor_before_evalute'] = '-';
		    $info_csdk['investor_after_evalute']  = '-';
		    $info_csdk['investor_amount']         = '-';
		    $info_csdk['csdk_investor_amount']    = '-';
		}
		
		// 获取此项目最新的投资金额（估值）和投资回报
		$deal_new = $GLOBALS['db']->getRow("select investor_after_evalute,investor_payback from ".DB_PREFIX."deal_trade_event WHERE deal_id={$deal_id} ORDER BY investor_time desc,id DESC LIMIT 1");
		
		// 柱状图区域
		$chart = array(
					'period'=>'',		# 轮次
					'evalute'=>'',		# 估值
					'amount'=>'',		# 融资额
				);
		
		// 融资信息
		$trade = $GLOBALS['db']->getAll(" 
 		SELECT event.id,period,investor_time ,investor_before_evalute,investor_after_evalute,sum(investor_amount) as investor_amount ,sum(investor_rate) as investor_rate 
FROM ".DB_PREFIX."deal_trade_event AS event left join  ".DB_PREFIX."deal_trade_fund_relation AS fund on (event.id=fund.deal_trade_event_id)
		WHERE 
`event`.deal_id={$deal_id}  and investor_record_type=2   
and `event`.deal_id=fund.deal_id group by event.id ORDER BY investor_time asc,id asc");
		if($trade){
			foreach($trade as $k=>$v){
				
				$period_lists=$GLOBALS['db']->getOne("select name from cixi_deal_period where id='{$v['period']}'") ;
				$trade[$k]['period']=$period_lists;
				$chart['period']    .="'$period_lists'".",";
				$chart['evalute']   .= $v['investor_after_evalute'].", ";
				$chart['amount']    .= $v['investor_amount'].", ";
				$trade[$k]['event'] = $GLOBALS['db']->getAll(" 
				select fund.id as fund__id,short_name,relation.is_csdk_fund as is_csdk_fund,investor_amount,investor_rate from cixi_deal_trade_fund_relation as  relation,cixi_fund as fund
where 
fund.id=relation.fund_id and relation.deal_id={$deal_id} and relation.deal_trade_event_id={$v['id']}
order by create_time desc  ,relation.id desc");
				
				foreach($trade[$k]['event'] as $key=>$val){
					//var_dump((int)$v['is_csdk_fund']);var_dump((int)$val['fund__id']);var_dump($fund_id);
					if ( $val['fund__id']==$fund_id ){
						$trade[$k]['is_csdk_partake'] = 1;
						 
						$trade[$k]['fund_short_name']=$val['short_name'];
					}
				}
				
			}
		}
		// 核心团队 新闻动态（接口请求）
		$user_id = $GLOBALS['user_info']['id'];
		$msgNum = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."user_notify where user_id = {$user_id} and is_read=0");
		$GLOBALS['tmpl']->assign("msgNum",$msgNum);
		$GLOBALS['tmpl']->assign("invest_info",$invest_info);
		$GLOBALS['tmpl']->assign("deal_info",$deal_info);
		$GLOBALS['tmpl']->assign("info_csdk",$info_csdk);
		$GLOBALS['tmpl']->assign("deal_new",$deal_new);
		$GLOBALS['tmpl']->assign("chart",$chart);
		$GLOBALS['tmpl']->assign("trade",$trade);
		$GLOBALS['tmpl']->assign("page_title","基金投资项目");
		$GLOBALS['tmpl']->assign("pageType",PAGE_MENU_USER_CENTER);
		$GLOBALS['tmpl']->assign("sideType",SIDE_MENU_INVESTED);
		$GLOBALS['tmpl']->display("invested_deal.html");
	}
	
	/*
	 * 个人中心-我的投资-项目详情页（ta接口b切换）
	* @param string $flag 	tab类型标识 (teams news)
	* @param int $deal_id 	项目ID
	*/
	public function deal_more(){
		$flag = isset($_POST['flag'])? strim($_POST['flag']) : "";
		$deal_id =  isset($_POST['deal_id'])? intval($_POST['deal_id']) : 0;
		if(!$flag || $deal_id == 0){
			$res['status'] 	= 99;
			$res['info'] 	= "Error:参数丢失";
			ajax_return($res);
		}
		
		$res = array('status'=>0,'info'=>'');
		
		// 核心团队
		if($flag == "teams"){
			$team = $GLOBALS['db']->getAll("select img_logo,name,title,brief from ".DB_PREFIX."deal_team where deal_id = {$deal_id} order by id asc limit 5");
// 			if($team){
// 				foreach ($team as $k=>$v){
// 					$team[$k]['img_logo'] = getQiniuPath($v['img_logo'],'img').'?imageView2/1/w/82/h/82';
// 				}
// 			}
			$res['status'] 	= 1;
			$res['info'] 	= $team;
		}
		
		// 新闻动态
		if($flag == "news"){
			$channel = app_enum_conf('BANNER_CHANNEL');
			
			// 公司新闻
			$news    = $GLOBALS['db']->getAll("select id,n_title,create_time from ".DB_PREFIX."news where n_deal = {$deal_id} and n_channel in ({$channel[1]['id']},{$channel[2]['id']}) and n_publish_state=2 order by id desc LIMIT 0,10");
			$newsId  = array();
			if($news){
				foreach ($news as $k=>$v){
					$news[$k]['create_time']   = to_date($v['create_time'],'Y年m月d日');
					$news[$k]['url']           = url("news",array('id'=>$v['id']));
					$newsId[]                  = $v['id'];
				}
			}else{
			    $res['status'] 	= 22;    # 没有公司新闻
			}
			
			// 行业新闻
			$cate_id 	= $GLOBALS['db']->getAll("select cate_id from ".DB_PREFIX."deal_select_cate where deal_id={$deal_id}");
			 
			$cate_deal_list=  "";
			if(!empty($cate_id))
			{
				foreach ($cate_id as $key => $val)
				{  
					$cate_deal_list= $cate_deal_list.' FIND_IN_SET("'.$val['cate_id'].'",n_cate) or';
				}
			}
			$cate_deal_list=(substr($cate_deal_list,0,strlen($cate_deal_list)-2));
			
			 
			if($newsId){
    			$newsId = implode(",",$newsId);	    # 组装公司新闻ID,需要排除已展示的
    			$map = "id not in({$newsId}) and";
			}else{
			    $map = "";
			}
			
			$news_cate 	= $GLOBALS['db']->getAll("select id,n_title,create_time from cixi_news where {$map}   ({$cate_deal_list}) and n_channel in ({$channel[1]['id']},{$channel[2]['id']}) and n_publish_state=2 order by id desc LIMIT 0,10");
			if($news_cate){
				foreach ($news_cate as $k=>$v){
					$news_cate[$k]['create_time']  = to_date($v['create_time'],'Y年m月d日');
					$news_cate[$k]['url']          = url("news",array('id'=>$v['id']));
				}
			}else{
			    $res['status'] 	= $res['status'] == 22 ? 33 : 44;    # 没有行业新闻
			}
			
			// 0 说明都有数据， 22 说明公司新闻没有， 33说明都没有，44 说明行业新闻没有
			$res['status'] 	= $res['status'] == 0 ? 2 : $res['status'];
			$res['info'] 	= array('news'=>$news,'news_cate'=>$news_cate);
		}
		
		ajax_return($res);
	}
}
?>