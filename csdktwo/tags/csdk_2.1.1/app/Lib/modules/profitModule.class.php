<?php
// +----------------------------------------------------------------------
// | 个人中心-我的投资收益相关
// +----------------------------------------------------------------------
// | Copyright (c) 2016 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class profitModule extends BaseModule {
    
	public function index() {
	    
		$user_id = (int)$GLOBALS ['user_info'] ['id'];
		$home_is_review =  $GLOBALS['db']->getOne("select is_review from ".DB_PREFIX."user where id = {$user_id}");
		if(empty($user_id)){
		    // 404
		    app_redirect(url("error#index"));
		}
		
		$params = array(
		    "uid"       => $user_id,
		);
		$result = request_service_api("Contents.Profit.ProfitList",$params);
		if($result['status'] == 0){
		    
		    foreach ($result['response']['profit_list'] as $k=>&$v) {
		        // 成立日期
		        $v['establish_date'] = date("Y-m-d",$v['establish_date']);
		        // 基金规模
		        $v['total_amount'] = number_format($v['total_amount']);
		        // 税前收益
		        $v['fund_income'] = $v['fund_income']=='-'?'-':number_format($v['fund_income']);
		        
		        if($v['user_type'] == 1){
		            // 购买日期
    		        $v['create_time'] = date("Y-m-d",$v['create_time']);
    		        // 购买金额
    		        $v['investor_amount'] = number_format($v['investor_amount']);
		            $profitListI[$k] = $v;
		        }
		        
		        if($v['user_type'] == 3){
		            // 募集金额
    		        $v['sum_investor_amount'] = number_format($v['sum_investor_amount']);
		            $profitListP[$k] = $v;
		        }
		    }
		    
		    unset($v);
		    
		    $result['response']['compute_formula'] = nl2br($result['response']['compute_formula']);
        	
		    $GLOBALS['tmpl']->assign("profitListI",$profitListI);
        	$GLOBALS['tmpl']->assign("profitListP",$profitListP);
        	$GLOBALS['tmpl']->assign("profitList",$result['response']);
		}elseif($result['status'] == 20){
		    
			$nope_profit='1';
		    
		}else{
		    // 404
		    app_redirect(url("error#index"));
		}

		
		$GLOBALS ['tmpl']->assign ("page_title", "我的收益" );
		$GLOBALS ['tmpl']->assign ("pageType", PAGE_MENU_PROFIT);
		$GLOBALS ['tmpl']->assign ("sideType", SIDE_MENU_PROFIT);
		$GLOBALS['tmpl']->assign("nope_profit",$nope_profit);
		$GLOBALS['tmpl']->assign("home_is_review",$home_is_review);
		$GLOBALS['tmpl']->display("invested_profit.html");		

	}
	
	public function analy(){
	    
	    $user_id = (int)$GLOBALS ['user_info'] ['id'];
	    $fund_id =  isset($_REQUEST['fund_id'])? intval($_REQUEST['fund_id']) : 0;
	    
	    if(empty($user_id) || empty($fund_id)){
	        $res['status'] 	= 0;
	        $res['info'] 	= "Error:参数丢失";
	    }else{
	        
	        $params = array(
	            "uid"       => $user_id,
	            "fund_id"   => $fund_id,
	        );
	        $result = request_service_api("Contents.Profit.ProfitInfo",$params);
	        
	        if($result['status'] == 0){
	            // 总收益
	            $sum_investor_profit = $result['response']['sum_investor_profit'];
	            
	            foreach($result['response']['deal_list'] as $v){
	                $deal_name[]   = $v['deal_name'];
	                $fund_income[] = $v['fund_income'];
	                $assist_rate[] = $v['assist_rate'];
	            }
	            
	            $res['status'] 	= 99;
	            $res['info'] 	= 'success';
	            $res['data'] 	= array('sum_investor_profit'=>$sum_investor_profit,'deal_name'=>$deal_name,'fund_income'=>$fund_income,'assist_rate'=>$assist_rate);
	        }else{
	            $res['status'] 	= 1;
	            $res['info'] 	= $result['message'];
	        }
	        
	    }
	    
        ajax_return($res);
	}
}
?>