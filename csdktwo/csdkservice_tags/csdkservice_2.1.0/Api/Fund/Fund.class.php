<?php
namespace  Api\Fund;

use System\Base;
use System\Logs;

class Fund extends Base{
	
	/**
	 * 更新基金的最新公允价值及尚未投出的金额
	 * @param unknown $fundId 基金ID
	 * @param unknown $fund_last_fair_value 基金最新公允价值
	 * @param unknown $fund_balance_amount 基金尚未投资金额
	 * @return \System\false
	 */
	public function  updateDealInvestAmount($fundId,$fund_last_fair_value,$fund_balance_amount){
		$fundId = isset($fundId)?$fundId:'';
		$fund_last_fair_value = isset($fund_last_fair_value)?$fund_last_fair_value:'';
		$fund_balance_amount = isset($fund_balance_amount)?$fund_balance_amount:'';
		if(empty($fundId) || empty($fund_last_fair_value) || empty($fund_balance_amount)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			$this->endResponse('',1001);
		}
	
		// 更新基金的
		$args = ['fund_id'=>$fundId,'balance_amount'=>$fund_balance_amount,'fair_value'=>$fund_last_fair_value];
		$sql = "UPDATE cixi_fund SET balance_amount = :balance_amount,fair_value = :fair_value WHERE id = :fund_id";
		$this->executeNonQuery($sql,$args);
	}
	
	/**
	 * 计算基金所投项目的回报数据
	 * @param unknown $fundId 基金ID
	 * @param unknown $fund_last_fair_value 基金最新公允价值
	 * @param unknown $fund_total_amount 基金投资金额
	 * @return \System\false 
	 */
	public function  calcFundInvestReurn($fundId,$fund_last_fair_value,$fund_total_amount){
		$fundId = isset($fundId)?$fundId:'';
		$fund_last_fair_value = isset($fund_last_fair_value)?$fund_last_fair_value:'';
		$fund_total_amount = isset($fund_total_amount)?$fund_total_amount:'';
		if(empty($fundId) || empty($fund_last_fair_value) || empty($fund_total_amount)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			$this->endResponse('',1001);
		}
		
		// 最高回报
		$args = ['fund_id'=>$fundId];
		$sql = "SELECT fund_id,investor_payback AS max_payback FROM cixi_deal_trade_fund_relation WHERE fund_id= :fund_id ORDER BY investor_payback DESC LIMIT 1";
		$max_payback = $this->getOne($sql,$args)['max_payback'];
		
		// 平均回报
		$args = ['fund_id'=>$fundId];
		$sql = "SELECT
					deal_id,
					investor_after_evalute * investor_rate/investor_amount AS payback_amount
				FROM
					(
						SELECT
							dt.deal_id,
							dt.investor_after_evalute,
							investor_rate,
							investor_amount,
							dt.investor_time
						FROM
							cixi_deal_trade_event AS dt
						INNER JOIN (
							SELECT
								deal_id,
								fund_id,
								sum(investor_rate) / 100 AS investor_rate,
								sum(investor_amount) AS investor_amount
							FROM
								cixi_deal_trade_fund_relation
							WHERE
								fund_id = :fund_id
							GROUP BY
								deal_id
						) AS fund_invest_deal ON dt.deal_id = fund_invest_deal.deal_id
						ORDER BY
							investor_time DESC
					) AS deal_invest
				GROUP BY
					deal_invest.deal_id";
		$average_payback = $this->executeQuery($sql,$args);
		
		$fund_total_payback_amount = 0.00;
		foreach ($average_payback as $average_payback_sub){
			$fund_total_payback_amount += $average_payback_sub['payback_amount'];
		}
		
		$average_payback = $fund_total_payback_amount/count($average_payback);
		
		
		// 整体回报
		$total_payback = $fund_last_fair_value/$fund_total_amount;
		
		$args = ['max_payback'=>$max_payback,'average_payback'=>$average_payback,'total_payback'=>$total_payback,'fund_id'=>$fundId];
		$sql = "UPDATE cixi_fund SET max_payback=:max_payback,average_payback=:average_payback,total_payback=:total_payback WHERE id=:fund_id";
		return $this->executeNonQuery($sql,$args);
	}
	
	/**
	 * 计算基金投资人的账面税前收益
	 * @param unknown $fundId 基金ID
	 * @param unknown $fund_last_fair_value 基金最新公允价值
	 * @param unknown $fund_total_amount 基金规模
	 * @param unknown $fund_income_rate_invester LP基金收益分配比例
	 */
	public function calcInvestorIncomeOfFund($fundId,$fund_last_fair_value,$fund_total_amount,$fund_income_rate_invester){
		$fundId = isset($fundId)?$fundId:'';
		$fund_last_fair_value = isset($fund_last_fair_value)?$fund_last_fair_value:'';
		$fund_total_amount = isset($fund_total_amount)?$fund_total_amount:'';
		$fund_income_rate_invester = isset($fund_income_rate_invester)?$fund_income_rate_invester:'';
		if(empty($fundId) || empty($fund_last_fair_value) || empty($fund_total_amount) || empty($fund_income_rate_invester)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			$this->endResponse('',1001);
		}
		
		$args = ['fund_id'=>$fundId,'fund_last_fair_value'=>$fund_last_fair_value,'fund_total_amount'=>$fund_total_amount,'fund_income_rate_invester'=>$fund_income_rate_invester];
		$sql = "UPDATE cixi_user_fund_relation SET fund_income = (:fund_last_fair_value - :fund_total_amount) * :fund_income_rate_invester/100 * investor_rate/100
		WHERE fund_id= :fund_id AND user_type=1";
		return $this->executeNonQuery($sql,$args);
	}
	
	/**
	 * 计算某只基金各合伙人的carray收益
	 * @param unknown $fundId 基金ID
	 * @param unknown $fund_last_fair_value 基金最新公允价值
	 * @param unknown $fund_total_amount 基金规模
	 * @param unknown $fund_income_rate_partner 合作人基金收益分配比例
	 */
	public function calcPartnerIncomeOfFund($fundId,$fund_last_fair_value,$fund_total_amount,$fund_income_rate_partner){
		$fundId = isset($fundId)?$fundId:'';
		$fund_last_fair_value = isset($fund_last_fair_value)?$fund_last_fair_value:'';
		$fund_total_amount = isset($fund_total_amount)?$fund_total_amount:'';
		$fund_income_rate_partner = isset($fund_income_rate_partner)?$fund_income_rate_partner:'';
		if(empty($fundId) || empty($fund_last_fair_value) || empty($fund_total_amount) || empty($fund_income_rate_partner)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			$this->endResponse('',1001);
		}

		// 更新投资人所属合伙人的carray收益
		$args = ['fund_id'=>$fundId];
		$sql = "SELECT partner_user_id,SUM(investor_rate) AS investor_rate FROM cixi_user_fund_relation WHERE fund_id= :fund_id AND user_type=1 GROUP BY partner_user_id HAVING partner_user_id > 0";
		$fund_partner_users = $this->executeQuery($sql,$args);
		foreach($fund_partner_users as $fund_partner_user){
			$partner_invest_rate = $fund_partner_user['investor_rate'];
			$partner_fund_income =  ($fund_last_fair_value - $fund_total_amount) * $fund_income_rate_partner/100 * $partner_invest_rate/100;
			$args = ['user_id'=>$fund_partner_user['partner_user_id'],'user_type'=>3,'fund_id'=>$fundId,'fund_income'=>$partner_fund_income];
			$sql = "REPLACE INTO cixi_user_fund_relation(user_id,user_type,fund_id,fund_income) VALUES(:user_id,:user_type,:fund_id,:fund_income)";
			$fund_user_income_result = $this->executeNonQuery($sql,$args);
		}
	}
	
	/**
	 * 查找磁斯达克投资某项目的所有基金信息
	 * @param unknown $dealId
	 */
	public function getCsdkFundByInvestDealId($dealId){
		$dealId = isset($dealId)?$dealId:'';
		if(empty($dealId)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			return $this->endResponse('',1001);
		}
		$args = ['deal_id'=>$dealId];
		$sql = "SELECT dtf.fund_id,dtf.deal_id,dtf.deal_trade_event_id,cf.total_amount,cf.fund_income_rate_cisdaq,cf.fund_income_rate_director,cf.fund_income_rate_invester,cf.fund_income_rate_partner 
				FROM cixi_deal_trade_fund_relation as dtf INNER JOIN cixi_fund AS cf ON dtf.fund_id = cf.id
				WHERE dtf.deal_id= :deal_id AND dtf.is_csdk_fund=1 GROUP BY dtf.fund_id";
		return $this->executeQuery($sql,$args);
	}
	
	
	/**
	 * 查找某只基金投资项目的投资额及所占项目的股份比例
	 * @param unknown $fundId 基金ID
	 * @return void|\System\mixed
	 */
	public function getInvestDealAmountByFundId($fundId){
		$fundId = isset($fundId)?$fundId:'';
		if(empty($fundId)){
			if($this->isInTransaction()){
				$this->rollbackTransaction();
			}
			return $this->endResponse('',1001);
		}
		$args = ['fund_id'=>$fundId];
		$sql = "SELECT dtf.fund_id,dtf.deal_id,sum(dtf.investor_amount) AS total_invest_amount,sum(dtf.investor_rate) AS total_invest_rate
				FROM   cixi_deal_trade_fund_relation AS dtf
				WHERE  dtf.fund_id= :fund_id
				GROUP BY dtf.deal_id";
		return $this->executeQuery($sql,$args);
	}
	
	
}