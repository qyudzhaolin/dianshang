<?php
class profitModule extends BaseModule {
	public function index() {
		$user_id = $GLOBALS ['user_info'] ['id'];
		$home_is_review = $GLOBALS ['db']->getOne ( "select is_review from " . DB_PREFIX . "user where id = {$user_id}" );
		

		if ($home_is_review == "1") {
			// 账面预期总收益正好为0返回未有收益
			$profit_sql = "
			select
			sum(fund_income) as fund_income
			from
			cixi_user_fund_relation
			where
			user_id={$user_id}
			";
			
			$profit_result = $GLOBALS ['db']->getOne ( $profit_sql );
			
			if (empty ( $profit_result ) || $profit_result <= "0.00") {
				$user_type = '0'; // 未有收益
			} else {
				
				// 收益身份标记
				$sql_user_type = "
				        SELECT user_type
				    	FROM
				    	cixi_fund as fund ,
				    	cixi_user_fund_relation as userfund
				    	WHERE
				    	fund.id = userfund.fund_id
				    	and fund.is_delete=1
				    	and fund.status=2
				    	and userfund.user_id={$user_id}
				    	group by user_type
				    	";
				$result_user_type = $GLOBALS ['db']->getAll ( $sql_user_type );
				
				if (! empty ( $result_user_type )) {
					$user_type_item = "";
					foreach ( $result_user_type as $key => $val ) {
						$user_type_item = $user_type_item . $result_user_type [$key] ['user_type'] . ',';
					}
					$user_type_item = (substr ( $user_type_item, 0, strlen ( $user_type_item ) - 1 ));
					if ($user_type_item == "1") {
						$user_type = '1'; // 投资人有收益
					} elseif ($user_type_item == "3") {
						$user_type = '2'; // 渠道合伙人有收益
						
					} elseif ($user_type_item == "4") {
						$user_type = '4'; // 投资总监
	
					} elseif ($user_type_item == "1,4") {
						$user_type = '1'; // 投资总监+投资人
					}
					 elseif ($user_type_item == "3,4") {
						$user_type = '2'; // //投资总监+渠道合伙人
					} else {
						$user_type = '3'; // 投资总监
					}
				} else {
					$user_type = '0'; // 未有收益
				}
			}
		}
		 
		
		//基金出资份额（税前收益） 投资人或者渠道合伙投资人可见
		if($user_type=="1"||$user_type=="3"){
			$invest_fund_sql="
			SELECT
    		short_name,
		    investor_amount,
		    investor_rate,
    		fund_income,
    		establish_date,
    		create_time,
    		total_amount,
    		fair_value
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id
			and user_type=1
		    and fund.is_delete=1
		    and fund.status=2
    		and fund.is_csdk_fund=1
		    and userfund.user_id={$user_id}
			order by fund_income desc  ";
			$invest_list = $GLOBALS ['db']->getAll ( $invest_fund_sql );
			if (!empty($invest_list)){
				foreach ($invest_list as $k=>$v) {
					 
					 
					$invest_list[$k]['profit_ratio']  = number_format(($v['fund_income']/$v['investor_amount'])*100,2);
				}
			}
		}
		
		//基金carry分成（税前收益） 渠道合伙人或者渠道合伙投资人可见
		if($user_type=="2"||$user_type=="3"){
			$carry_fund_sql="
			SELECT
			fund_id,
    		short_name,
    		fund_income,
    		establish_date,
    		create_time,
    		total_amount,
    		fair_value,
    		fund_income_rate_partner
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id
			and user_type=3
		    and fund.is_delete=1
			and fund.is_csdk_fund=1
		    and fund.status=2
		    and userfund.user_id={$user_id}
			order by fund_income desc  ";
			$carry_list = $GLOBALS ['db']->getAll ( $carry_fund_sql );
 			
			if (!empty($carry_list)){
				foreach ($carry_list as $k=>$v) {
					$carry_list[$k]['assist_rate']  =  $GLOBALS['db']->getOne("select sum(investor_rate) from cixi_user_fund_relation where user_type=1 and partner_user_id={$user_id} and fund_id={$v['fund_id']}");
					$carry_list[$k]['sum_investor_amount']  =  $GLOBALS['db']->getOne("select sum(investor_amount) from cixi_user_fund_relation where user_type=1 and partner_user_id={$user_id} and fund_id={$v['fund_id']}");
				}
			}
		}
		
		//所有收益列表为空判断返回值
		if(empty($invest_list)&&empty($carry_list)){
			$no_profit='0';
		}
		else {
			$no_profit='1';
		}

		$GLOBALS['tmpl']->assign("invest_list",$invest_list);
		$GLOBALS['tmpl']->assign("carry_list",$carry_list);
		$GLOBALS['tmpl']->assign("home_is_review",$home_is_review);
		$GLOBALS['tmpl']->assign("no_profit",$no_profit);
		$GLOBALS ['tmpl']->assign ("page_title", "我的收益" );
		$GLOBALS ['tmpl']->assign ("pageType", PAGE_MENU_PROFIT);
		$GLOBALS ['tmpl']->assign ("sideType", SIDE_MENU_PROFIT);
		$GLOBALS['tmpl']->display("invested_profit.html");		

	}
}
?>