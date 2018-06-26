<?php
// +----------------------------------------------------------------------
// | 个人中心-我的投资相关
// +----------------------------------------------------------------------
// | Copyright (c) 2016 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class investedModule extends BaseModule{
	
	private $invested_rows = 10; # 我的投资-我购买的基金列表显示行数
	private $invest_deal_rows = 10; # 我的投资-基金投资项目列表显示行数
	private $attachment_rows = 5; # 我的投资-资质披露显示行数
	
	/*
	 * 个人中心-我的投资列表
	 */
	public function index(){
		$user_id = $GLOBALS['user_info']['id'];
		$home_is_review =  $GLOBALS['db']->getOne("select is_review from ".DB_PREFIX."user where id = {$user_id}");

		$sql_select="
					SELECT 
					fund.id as fund_id,
					fund.short_name as short_name,
				    managers.id as managers_id,
			    	managers.short_name as name,
					fund.total_amount as total_amount,
					userfund.investor_amount as investor_amount,
					fund.establish_date as establish_date
				  ";
		
		$sql_from="
				    from
					cixi_fund as fund ,
					cixi_user_fund_relation as userfund,
                    cixi_fund_managers as managers
				";
		
		$sql_where="
					WHERE
			        managers.id=fund.managers_id 
                    and	fund.id = userfund.fund_id 
					and fund.is_delete=1 
					and fund.status=2 
					and fund.is_csdk_fund=1
					and userfund.user_id=$user_id
				    ";
		
		$condition_invester	=" and userfund.user_type=1";//投資列表
		$condition_partner	=" and userfund.user_type=3";//渠道合夥人
		$condition_director	=" and userfund.user_type=4";//投資總監
		$sql_order_by="
				order by fund.establish_date desc,fund.id desc
				";
		
		// 我的投资列表，LP可见，参与投资的渠道合伙人可見
        $invest_fund_sql = $sql_select . "  " . $sql_from . " " . $sql_where . " " . $condition_invester . " " . $sql_order_by;
        $invest_result = $GLOBALS['db']->getAll($invest_fund_sql);
        
        // 我的管理列表，投資總監可见，
        $partner_fund_sql = $sql_select . "  " . $sql_from . " " . $sql_where . " " . $condition_director . " " . $sql_order_by;
        $partner_result = $GLOBALS['db']->getAll($partner_fund_sql);
        
        // 我協助發起的基金列表，渠道合伙人
        $director_fund_sql = $sql_select . "  " . $sql_from . " " . $sql_where . " " . $condition_partner . " " . $sql_order_by;
        $director_result = $GLOBALS['db']->getAll($director_fund_sql);
        
        //所有基金列表为空判断返回值
        if(empty($invest_result)&&empty($partner_result)&&empty($director_result)){
        	$nop_list='0';
        }
        else {
        	$no_list='1';
        }
		
        
        $GLOBALS['tmpl']->assign("invest_result",$invest_result);
        $GLOBALS['tmpl']->assign("partner_result",$partner_result);
        $GLOBALS['tmpl']->assign("director_result",$director_result);
        $GLOBALS['tmpl']->assign("home_is_review",$home_is_review);
        $GLOBALS['tmpl']->assign("no_list",$no_list);
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
		$user_type =  isset($_GET['id2'])? intval($_GET['id2']) : 1;
	 
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
where     fund_id={$id} and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id  ");
		$invested['amount_total'] = $amount_total ? $amount_total : 0;
		
		// 我的投资信息
		$invested['my_invested'] = $GLOBALS['db']->getRow("select investor_amount,investor_rate from ".DB_PREFIX."user_fund_relation where fund_id={$id} and user_id={$user_id}");
		
		// 基金管理人公司
		$managers = $GLOBALS['db']->getRow("select managers.id as managers_id,managers.name as managers_name from ".DB_PREFIX."fund_managers as managers,".DB_PREFIX."fund as fund where managers.id=fund.managers_id and fund.id={$id}  ");
		
		// 核心管理团队
		$manage_team = $GLOBALS['db']->getAll("
			select 
			fund.position as position,fund.brief as summary,team.user_logo as head_logo,users.user_name as name
			from 
			".DB_PREFIX."user_fund_relation as fund,
			".DB_PREFIX."fund_managers_team as team,
			".DB_PREFIX."user as users
			 where 
			fund.user_type=4 
			and fund.user_id= users.id
			and  fund.fund_id={$id}
			and fund.managers_team_id	 = team.id and team.is_del=1
			order by is_director asc,fund.id asc");
		
		// 基金投资项目列表
		$sql ="select event.deal_id,relation.fund_id,sum(investor_amount) as investor_amount,Max(investor_date) as investor_date,deal.s_name,deal.name 
from ".DB_PREFIX."deal as deal left join ".DB_PREFIX."deal_trade_fund_relation as relation on deal.id=relation.deal_id,".DB_PREFIX."deal_trade_event as event 
where   fund_id={$id} and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id 
GROUP BY deal.id,relation.deal_id order by relation.sort asc,investor_amount desc ,investor_date desc  limit {$this->invest_deal_rows}";
		$deal = $GLOBALS['db']->getAll($sql);
		 
		$my_deal_more = 0;
		if($deal){
			foreach($deal as $k=>$v){
				$deal[$k]['valuations']         =  number_format($GLOBALS['db']->getOne("select investor_after_evalute from ".DB_PREFIX."deal_trade_event where deal_id={$v['deal_id']}  order by investor_time desc limit 1 "));
				$deal[$k]['investor_amount']    = number_format($v['investor_amount']);
				$deal[$k]['investor_date']      = to_date($v['investor_date'],'Y.m.d');
			}
			$my_deal_count   = $GLOBALS['db']->getAll("select deal.id from ".DB_PREFIX."deal as deal left join ".DB_PREFIX."deal_trade_fund_relation as relation on deal.id=relation.deal_id,".DB_PREFIX."deal_trade_event as event 
where    fund_id={$id} and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id 
GROUP BY deal.id,relation.deal_id order by relation.sort asc,investor_amount desc ,investor_date desc");
			 
			$my_deal_more    = count($deal) < count($my_deal_count) ? 1 : 0;	  # 加载更多按钮显示(1)隐藏(0)标识位
		} 
		 
		$msgNum = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."user_notify where user_id = {$user_id} and is_read=0");
		
		//基金信息及资质披露
		$sql_attachment = " SELECT id, title, attachment, type
							FROM cixi_fund_attachment
							WHERE fund_id = {$id} and is_del = 1 order by publish_time desc ,id desc
					        limit {$this->attachment_rows}";
		$attachment_result = $GLOBALS['db']->getAll($sql_attachment);
		$attachment_more = 0;
		if($attachment_result){
		    foreach($attachment_result as $k=>$v){
		        if($v['type'] == 1){
		            $attachment_result[$k]['href'] = getQiniuPath($v['attachment'], "img");
		        }elseif($v['type'] == 2){
		            $attachment_result[$k]['href'] = getQiniuPath($v['attachment'], "bp");
		        }
		    }
		    
    		$attachment_count = $GLOBALS['db']->getOne("SELECT count(1) FROM cixi_fund_attachment WHERE fund_id = {$id} and is_del = 1");
    		$attachment_more = count($attachment_result) < $attachment_count ? 1 : 0;	  # 加载更多按钮显示(1)隐藏(0)标识位
		}
		if ($user_type == 4) {
			$fund_title="我管理的基金";
		}
		elseif ($user_type == 3) {
			$fund_title="我协助发起的基金";
		}
		else{
			$fund_title="我投资的基金";
		}
		$GLOBALS['tmpl']->assign("msgNum",$msgNum);
		$GLOBALS['tmpl']->assign("invested",$invested);
		$GLOBALS['tmpl']->assign("managers",$managers);
		$GLOBALS['tmpl']->assign("manage_team",$manage_team);
		$GLOBALS['tmpl']->assign("deal",$deal);
		$GLOBALS['tmpl']->assign("my_deal_more",$my_deal_more);
		$GLOBALS['tmpl']->assign("attachment_result",$attachment_result);
		$GLOBALS['tmpl']->assign("attachment_more",$attachment_more);
		$GLOBALS['tmpl']->assign("fund_title",$fund_title);
		$GLOBALS['tmpl']->assign("page_title","基金详情");
		$GLOBALS['tmpl']->assign("pageType",PAGE_MENU_USER_CENTER);
		$GLOBALS['tmpl']->assign("sideType",SIDE_MENU_INVESTED);
		$GLOBALS['tmpl']->display("invested_fund.html");
	}
	
	/*
	 * 个人中心-我的投资-基金详情页（投资项目加载更多接口）
	* @param int $id 		基金ID
	* @param int $counter 	基金条数计数器
	*/	
	public function invested_fund_more(){
		
		$id =  isset($_POST['id'])? intval($_POST['id']) : 0;
		
		if($id){
			$counter =  isset($_POST['counter'])? intval($_POST['counter']) : 0;
			$res = array('status'=>99,'info'=>0,'data'=>'','more'=>''); //用于返回的数据
				
		 
			$sql     = "select event.deal_id,relation.fund_id,sum(investor_amount) as investor_amount,Max(investor_date) as investor_date,deal.s_name,deal.name 
from ".DB_PREFIX."deal as deal left join ".DB_PREFIX."deal_trade_fund_relation as relation on deal.id=relation.deal_id,".DB_PREFIX."deal_trade_event as event 
where   fund_id={$id} and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id 
GROUP BY deal.id,relation.deal_id order by relation.sort asc,investor_amount desc ,investor_date desc limit  {$counter},{$this->invest_deal_rows}";
		 
			$lists = $GLOBALS['db']->getAll($sql);
			if (!empty($lists)){
				foreach ($lists as $k=>$v) {
					$lists[$k]['valuations']         =  number_format($GLOBALS['db']->getOne("select investor_after_evalute from ".DB_PREFIX."deal_trade_event where deal_id={$v['deal_id']}  order by investor_time desc limit 1 "));
					$lists[$k]['investor_amount']  = number_format($v['investor_amount']);
					$lists[$k]['investor_date']    = to_date($v['investor_date'],'Y.m.d');
					$lists[$k]['fund_url']         = url("invested#deal_details",array('fund_id'=>$v['fund_id'],'deal_id'=>$v['deal_id']));
				}
				$lists_count = $GLOBALS['db']->getALL("select deal.id from ".DB_PREFIX."deal as deal left join ".DB_PREFIX."deal_trade_fund_relation as relation on deal.id=relation.deal_id,".DB_PREFIX."deal_trade_event as event 
where   fund_id={$id} and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id 
GROUP BY deal.id,relation.deal_id order by relation.sort asc,investor_amount desc ,investor_date desc");
				$res['more'] = count($lists) + $counter < count($lists_count) ? 1 : 0;
				$res['data'] = $lists;
			}
		}else{
			$res['status'] 	= 0;
			$res['info'] 	= "Error:参数丢失";
		}
		
		ajax_return($res);
	}
	
	/*
	 * 个人中心-我的投资-基金详情页（资质披露加载更多接口）
	* @param int $id 		基金ID
	* @param int $counter 	基金条数计数器
	*/	
	public function attachment_more(){
		
		$id =  isset($_POST['id'])? intval($_POST['id']) : 0;
		
		if($id){
			$counter =  isset($_POST['counter'])? intval($_POST['counter']) : 0;
			$res = array('status'=>99,'info'=>0,'data'=>'','more'=>''); //用于返回的数据
			$lists = $GLOBALS['db']->getAll("SELECT id,title,attachment,type FROM ".DB_PREFIX."fund_attachment WHERE fund_id = {$id} and is_del = 1 order by publish_time desc ,id desc limit {$counter},{$this->attachment_rows}");
			if (!empty($lists)){
			    foreach($lists as $k=>$v){
			        if($v['type'] == 1){
			            $lists[$k]['href'] = getQiniuPath($v['attachment'], "img");
			            $lists[$k]['typeSrc'] = get_spec_image("JPG-icon.png");
			        }elseif($v['type'] == 2){
			            $lists[$k]['href'] = getQiniuPath($v['attachment'], "bp");
			            $lists[$k]['typeSrc'] = get_spec_image("pdf-icon.png");
			        }
			    }
				$lists_count = $GLOBALS['db']->getOne("SELECT count(1) FROM ".DB_PREFIX."fund_attachment WHERE fund_id = {$id} and is_del = 1");
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
		$deal_info = $GLOBALS['db']->getRow("select {$field} from ".DB_PREFIX."deal where id={$deal_id}  ");
		
		// 查询磁斯达克最新融资信息->获取 投资前估值,投资后估值,该轮融资总额 等数据
		$info_csdk = $GLOBALS['db']->getRow(" 
 		SELECT event.id,period,investor_before_evalute,investor_after_evalute ,investor_amount  as csdk_investor_amount  
FROM ".DB_PREFIX."deal_trade_event AS event,".DB_PREFIX."deal_trade_fund_relation as fund,".DB_PREFIX."deal_period as period 
WHERE 
`event`.deal_id={$deal_id}  and is_csdk_fund=1 and deal_trade_event_id=`event`.id and fund_id={$fund_id}
and  `event`.period=period.id
ORDER BY period.sort desc,`event`.id DESC LIMIT 0,1");
		
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
		$deal_new = $GLOBALS['db']->getRow("SELECT investor_after_evalute,event.id as event_id
FROM ".DB_PREFIX."deal_trade_event AS event  , ".DB_PREFIX."deal_trade_fund_relation as trade,".DB_PREFIX."deal_period as period 
WHERE `event`.deal_id={$deal_id}   and event.id=trade.deal_trade_event_id  and  `event`.period=period.id 
ORDER BY period.sort desc,event.id DESC LIMIT 0,1");
		
		$lastest_condition="";
		if(!empty($deal_new)){
			//$lastest_condition .= " and event.id < ({$deal_new['event_id']})";
			$lastest_condition .=" ";
		}else {
			$lastest_condition .=" ";
		}
		$lastest_select="
			SELECT trade.investor_payback as  investor_payback
FROM cixi_deal_trade_event AS event  , cixi_deal_trade_fund_relation as trade,".DB_PREFIX."deal_period as period 
WHERE `event`.deal_id={$deal_id}  and trade.fund_id={$fund_id}  and event.id=trade.deal_trade_event_id
and  `event`.period=period.id";
		
		$lastest_order_by = "ORDER BY period.sort desc,event.id DESC LIMIT 0,1";
		$lastest_sql = $lastest_select . " " . $lastest_condition . " " . $lastest_order_by;
	 
		$result_lastest = $GLOBALS['db']->getRow($lastest_sql);
		 
		// 柱状图区域
		$chart = array(
					'period'=>'',		# 轮次
					'evalute'=>'',		# 估值
					'amount'=>'',		# 融资额
				);
		
		// 融资信息
		$trade = $GLOBALS['db']->getAll(" 
 		SELECT event.id,period,investor_time ,investor_before_evalute,investor_after_evalute,sum(investor_amount) as investor_amount ,sum(investor_rate) as investor_rate 
FROM ".DB_PREFIX."deal_trade_event AS event left join  ".DB_PREFIX."deal_trade_fund_relation AS fund on (event.id=fund.deal_trade_event_id),".DB_PREFIX."deal_period as period 
		WHERE 
`event`.deal_id={$deal_id}     
and `event`.deal_id=fund.deal_id and  `event`.period=period.id group by event.id ORDER BY period.sort  asc,id asc");
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
		$GLOBALS['tmpl']->assign("result_lastest",$result_lastest);
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