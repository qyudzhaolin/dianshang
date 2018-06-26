<?php
class apiModule extends BaseModule
{
	 
	public function evalute(){                                                  
	 	$id = intval($_REQUEST ['id']);
 	 	$evalute_list = $GLOBALS['db']->getAll("SELECT per.`name` AS period,`event`.investor_after_evalute as investor_after_evalute
													FROM ".DB_PREFIX."deal_trade_event AS event ,".DB_PREFIX."deal_period as per
													WHERE `event`.deal_id=".$id."  AND per.id = event.period AND `event`.investor_record_type = 2
													ORDER BY event.investor_time asc,event.id asc") ;
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
		$amount_list = $GLOBALS['db']->getAll("SELECT `per`.name AS period, SUM(relation.investor_amount) AS investor_amount FROM ".DB_PREFIX."deal_trade_event AS event, ".DB_PREFIX."deal_trade_fund_relation AS relation,".DB_PREFIX."deal_period AS per
												WHERE `event`.id = relation.deal_trade_event_id AND per.id = event.period AND `event`.investor_record_type = 2 AND `event`.deal_id=".$id."
												GROUP BY `event`.id
												ORDER BY `event`.investor_time asc,`event`.id asc") ;
		if($amount_list){
			$GLOBALS['tmpl']->assign("amount_list",$amount_list);
		}
		$GLOBALS['tmpl']->display("api_chart_amount.html");
	
	}
 
}
?>