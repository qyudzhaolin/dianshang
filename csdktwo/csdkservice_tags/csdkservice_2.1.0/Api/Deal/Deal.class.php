<?php
namespace  Api\Deal;

use System\Base;
use System\Logs;

class Deal extends Base{
	/**
	 * 计算参与项目最新一轮投资的各支基金的占比
	 * @param unknown $dealId 项目ID
	 * @param unknown $last_financing_trade_id 项目融资记录ID
	 * @param unknown $last_financing_after_evalute 项目投后估值
	 * @return \System\false 成功返回更新条数，失败返回false
	 */
	public function calInvestorAccountingOfLastPeriod($dealId,$last_financing_trade_id,$last_financing_after_evalute){
		// 参数检查
		$dealId = isset($dealId)?$dealId:'';
		$last_financing_after_evalute = isset($last_financing_after_evalute)?$last_financing_after_evalute:'';
		$last_financing_trade_id = isset($last_financing_trade_id)?$last_financing_trade_id:'';
		if(empty($dealId) || empty($last_financing_after_evalute) || empty($last_financing_trade_id)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			$this->endResponse('',1001);
		}
		
		$last_financing_after_evalute = $last_financing_after_evalute /100;

		$args = ['deal_id'=>$dealId,'trade_id'=>$last_financing_trade_id,'after_evalute'=>$last_financing_after_evalute];
		$sql = "UPDATE cixi_deal_trade_fund_relation SET investor_rate = investor_amount/:after_evalute,investor_payback=1 WHERE deal_id= :deal_id AND deal_trade_event_id= :trade_id";
		return $this->executeNonQuery($sql,$args);
	}
	
	/**
	 * 计算参与项目历史各轮次投资的各支基金的占比
	 * @param unknown $dealId 项目ID
	 * @param unknown $last_financing_trade_id 项目融资记录ID
	 * @param unknown $last_financing_before_evalute 项目投前估值
	 * @param unknown $last_financing_after_evalute 项目投后估值
	 * @return \System\false  成功返回更新条数，失败返回false
	 */
	public function calInvestorAccountingOfHistoryPeriod($dealId,$last_financing_trade_id,$last_financing_before_evalute,$last_financing_after_evalute){
		// 参数检查
		$dealId = isset($dealId)?$dealId:'';
		$last_financing_after_evalute = isset($last_financing_after_evalute)?$last_financing_after_evalute:'';
		$last_financing_before_evalute = isset($last_financing_before_evalute)?$last_financing_before_evalute:'';
		$last_financing_trade_id = isset($last_financing_trade_id)?$last_financing_trade_id:'';
		if(empty($dealId) || empty($last_financing_before_evalute) || empty($last_financing_after_evalute) || empty($last_financing_trade_id)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			$this->endResponse('',1001);
		}
	
		// 按融资日期倒序查询本项目历史各融资轮次，需过滤掉当前轮次
		$args = ['deal_id'=>$dealId,'trade_id'=>$last_financing_trade_id];
		$sql = "SELECT
				  cdt.id,cdt.deal_id,cdt.period,cdt.investor_before_evalute,cdt.investor_after_evalute
				FROM
					cixi_deal_trade_event AS cdt INNER JOIN cixi_deal_period AS cdp ON cdt.period = cdp.id
				WHERE
					cdt.deal_id = :deal_id AND cdt.id != :trade_id
				ORDER BY
					cdp.sort DESC";
		$history_trade_records = $this->executeQuery($sql,$args);
		
		// 依此更新历史各轮次的投资基金的占比和回报
		// 按照轮次倒序依此计算，各轮次的投资占比计算公式：本轮次投资金额/本轮次投后估值*后面的各轮次投前估值/投后估值的乘积
		// 投资回报计算：最新占比*项目最新的投后估值/当前投资金额/100
		if (count($history_trade_records) > 0){
			$median_invest_rate = $last_financing_before_evalute/$last_financing_after_evalute;
			for ($i = 0,$l = count($history_trade_records);$i < $l;$i++){
				$history_trade_record = $history_trade_records[$i];
				// 首先更新前一个轮次的各投资基金的占比
				$args = ['median_invest_rate'=>$median_invest_rate,'deal_id'=>$dealId,'trade_id'=>$history_trade_record['id'],'current_after_evalute'=>$history_trade_record['investor_after_evalute'],'last_after_evalute'=>$last_financing_after_evalute];
				$sql = "UPDATE cixi_deal_trade_fund_relation
						SET investor_rate = investor_amount / :current_after_evalute * :median_invest_rate * 100,
						 	investor_payback = investor_rate * :last_after_evalute / (investor_amount * 100)
						WHERE
							deal_id = :deal_id AND deal_trade_event_id = :trade_id";
				$this->executeNonQuery($sql,$args);
				// 计算上一个轮次占比需要用到的中间投资占比
				$median_invest_rate *= $history_trade_record['investor_before_evalute']/$history_trade_record['investor_after_evalute'];
			}
		}
	}
	
	/**
	 * 查询本项目最新一个轮次的融资记录数据
	 * @param unknown $dealId
	 */
	public function getLastInvestRecord($dealId){
		$dealId = isset($dealId)?$dealId:'';
		if(empty($dealId)){
			return $this->endResponse('',1001);
		}
		$args = ['deal_id'=>$dealId];
		$sql = "SELECT dte.id as id,deal_id,period,investor_before_evalute,investor_after_evalute
				FROM cixi_deal_trade_event AS dte INNER JOIN cixi_deal_period as dp ON dte.period = dp.id 
				WHERE dte.deal_id= :deal_id ORDER BY dp.sort DESC LIMIT 1";
		return $this->getOne($sql,$args);
	}
	
	
	/**
	 * 查询项目某个轮次的融资总金额
	 * @param unknown $tradeId 交易记录
	 * @return void|\System\type
	 */
	public function getTotalInvestAmountByPeriod($tradeId){
		$tradeId = isset($tradeId)?$tradeId:'';
		if(empty($tradeId)){
			return $this->endResponse('',1001);
		}
		$args = array('trade_id'=>$tradeId);
		$sql = "SELECT deal_trade_event_id,sum(investor_amount) AS total_invest_amount
				FROM   cixi_deal_trade_fund_relation AS tf
				WHERE  deal_trade_event_id= :trade_id";
		return $this->getOne($sql,$args);
	}
	
	/**
	 * 更新项目各轮次的基金投资占比和，投资日期及投后估值
	 * @param unknown $dealId 项目ID
	 * @param unknown $last_financing_trade_id 项目融资记录ID
	 * @param unknown $last_financing_after_evalute 投后估值
	 */
	public function updateFinancingRateOfDealPeriod($dealId,$last_financing_trade_id,$last_financing_after_evalute){
		// 参数检查
		$dealId = isset($dealId)?$dealId:'';
		$last_financing_after_evalute = isset($last_financing_after_evalute)?$last_financing_after_evalute:'';
		$last_financing_trade_id = isset($last_financing_trade_id)?$last_financing_trade_id:'';
		if(empty($dealId) || empty($last_financing_after_evalute) || empty($last_financing_trade_id)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			$this->endResponse('',1001);
		}

		$args = ['deal_id'=>$dealId];
		$sql = "SELECT dtr.deal_trade_event_id,dtr.investor_date,SUM(dtr.investor_rate) as total_invest_rate  
				FROM (SELECT deal_trade_event_id,deal_id,investor_date,investor_rate FROM cixi_deal_trade_fund_relation WHERE deal_id = :deal_id  ORDER BY investor_date DESC) dtr
				GROUP BY deal_trade_event_id  ORDER BY investor_date DESC ";
		$query_results = $this->executeQuery($sql,$args);
		if(count($query_results) <=0){
			// 未查询到各融资记录的占比统计
			return $this->endResponse('',1002);
		}
		$exec_result = [];
		foreach($query_results as $one_result){
			if($one_result['deal_trade_event_id'] ==$last_financing_trade_id){
				$args = ['trade_id'=>$last_financing_trade_id,'after_evalute'=>$last_financing_after_evalute,'invest_date'=>$one_result['investor_date'],'invest_rate'=>$one_result['total_invest_rate']];
				$sql = "UPDATE cixi_deal_trade_event SET investor_after_evalute = :after_evalute,investor_time= :invest_date,evalute_growth_rate = :invest_rate
				WHERE id = :trade_id";
				$exec_result[] = $this->executeNonQuery($sql,$args);
			} else{
				$args = ['trade_id'=>$one_result['deal_trade_event_id'],'invest_rate'=>$one_result['total_invest_rate']];
				$sql = "UPDATE cixi_deal_trade_event SET evalute_growth_rate = :invest_rate
				WHERE id = :trade_id";
				$exec_result[] = $this->executeNonQuery($sql,$args);
			}
		}
	}
	
	
	
	
	
}

