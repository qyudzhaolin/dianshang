<?php
class apiModule extends BaseModule
{
	 
	public function evalute(){                                                  
	 	$id = intval($_REQUEST ['id']);
 	 	$evalute_list = $GLOBALS['db']->getAll("SELECT period,investor_after_evalute FROM ".DB_PREFIX."deal_trade_event AS event  WHERE `event`.deal_id=".$id."  ORDER BY investor_time asc,id asc") ;
		if($evalute_list){
			$GLOBALS['tmpl']->assign("evalute_list",$evalute_list);
		}
		$GLOBALS['tmpl']->display("api_chart_evalute.html"); 
	}
	
	public function rate(){
		$id = intval($_REQUEST ['id']);
		$rate_list = $GLOBALS['db']->getAll("SELECT period,evalute_growth_rate FROM ".DB_PREFIX."deal_trade_event AS event  WHERE `event`.deal_id=".$id."  ORDER BY investor_time asc,id asc") ;
		if($rate_list){
			$GLOBALS['tmpl']->assign("rate_list",$rate_list);
		}
		$GLOBALS['tmpl']->display("api_chart_rate.html");
 
	}
	
	public function amount(){
		$id = intval($_REQUEST ['id']);
		$amount_list = $GLOBALS['db']->getAll("SELECT period,investor_amount FROM ".DB_PREFIX."deal_trade_event AS event  WHERE `event`.deal_id=".$id."  ORDER BY investor_time asc,id asc") ;
		if($amount_list){
			$GLOBALS['tmpl']->assign("amount_list",$amount_list);
		}
		$GLOBALS['tmpl']->display("api_chart_amount.html");
	
	}
 
}
?>