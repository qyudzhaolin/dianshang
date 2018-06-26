<?php

namespace Api\Deal;

use System\Base;
use System\Logs;

class NewDealInvestCalc extends Base {
	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * 计算逻辑
	 * 查询该项目的所有轮次的融前估值等数据,并按轮次倒序排列
	 * 检查最新轮次有没实际的基金投资数据，没有则结束计算，
	 * 有 则开始计算该基金最新轮次的实际融资总金额，并更新该轮次的投后估值及投资占比
	 * 并取出该轮次的所有融资记录，并按投资日期倒序，计算每条融资记录的投后占比，把投资日期更新到轮次的融资完成日期
	 * 更新该项目的历史各轮次各支基金的投资占比，及投资回报
	 * 计算历史各轮次的最新投资占比，各回报的平均数
	 * 计算与该项目相关的各基金的收益
	 * 计算与该项目相关各基金的最好投资回报、平均投资回报以及总体回报
	 */
	public function calcDealData($params) {
		Logs::info ( 'Deal.DealTradeCalculate', LOG_FLAG_NORMAL, [ 
				'开始计算项目各投资基金股权占比及投资回报，传入参数：',
				$params 
		] );
		// 开启事务
		$this->startTransaction ();
		try {
			$dealId = isset ( $params ['dealId'] ) ? $params ['dealId'] : '';
			if (empty ( $dealId )) {
				return $this->endResponse ( null, 1001 );
			}
			
			$deal_obj = new Deal ();
			// 查询项目最新融资记录，如果不存在，则直接返回错误！
			$last_financing = $deal_obj->getLastInvestRecord ( $dealId );
			if (empty ( $last_financing )) {
				return $this->endResponse ( null, 1002 );
			}
			
			// 查询项目最新轮次的融资总金额数据
			$last_financing_amount_result = $deal_obj->getTotalInvestAmountByPeriod ( $last_financing ['id'] );
			$last_financing_amount = $last_financing_amount_result ['total_invest_amount'];
			if (empty ( $last_financing_amount )) {
				return $this->endResponse (null, 1002 );
			}
			
			$last_financing_before_evalute = $last_financing ['investor_before_evalute'];
			// 最新轮次融后估值
			$last_financing_after_evalute = $last_financing_amount + $last_financing_before_evalute;
			
			// 更新该项目最新轮次投资基金的投资占比及投资回报
			$fund_invest_rate_last_period = $deal_obj->calInvestorAccountingOfLastPeriod ( $dealId, $last_financing ['id'], $last_financing_after_evalute );
			// 更新该项目历史轮次各投资基金的投资占比及投资回报
			$fund_invest_rate_history_period = $deal_obj->calInvestorAccountingOfHistoryPeriod ( $dealId, $last_financing ['id'], $last_financing_before_evalute, $last_financing_after_evalute );
			
			// 更新该项目的各轮次投资占比、回报、融资完成日期等数据
			$deal_financing_rate = $deal_obj->updateFinancingRateOfDealPeriod ( $dealId, $last_financing ['id'], $last_financing_after_evalute );
			
			// 查找本项目相关的所有磁斯达克基金,并依此计算各只基金的投资人的收益数据
			$fund_obj = new \Api\Fund\Fund ();
			$invest_funds = $fund_obj->getCsdkFundByInvestDealId ( $dealId );
			
			if (count ( $invest_funds ) > 0) {
				foreach ( $invest_funds as $invest_fund ) {
					$fund_id = $invest_fund ['fund_id'];
					$fund_invest_deals = $fund_obj->getInvestDealAmountByFundId ( $fund_id );
					
					$fund_total_amount = $invest_fund ['total_amount']; // 基金规模
					$fund_have_invest_total_amount = 0; // 基金已投资项目的总金额
					$fund_have_invest_newest_amount = 0; // 基金所投项目的最新价值

					$deal_income_datas = array();
					// 循环累加基金所投每个项目的投资金额及最新价值
					foreach ( $fund_invest_deals as $fund_invest_deal ) {
						$fund_have_invest_total_amount += $fund_invest_deal ['total_invest_amount'];
						// 计算已投单个项目的最新价值
						$deal_latest_evalute = $deal_obj->getLastInvestRecord ( $fund_invest_deal ['deal_id'] );
						
						// 该基金所投单个项目的回报
						$deal_income_datas[ $fund_invest_deal ['deal_id'] ] = $deal_latest_evalute ['investor_after_evalute'] * $fund_invest_deal ['total_invest_rate'] / 100 - $fund_invest_deal ['total_invest_amount'];
						
						$fund_have_invest_newest_amount += $deal_latest_evalute ['investor_after_evalute'] * $fund_invest_deal ['total_invest_rate'] / 100;
					}
					// 基金账面剩余资金
					$fund_acount_balance = $fund_total_amount - $fund_have_invest_total_amount;
					// 基金最新公允价值
					$fund_last_fair_value = $fund_acount_balance + $fund_have_invest_newest_amount;
					
					// 收益分配比例
					$fund_income_rate_invester = $invest_fund ['fund_income_rate_invester'];
					$fund_income_rate_partner = $invest_fund ['fund_income_rate_partner'];
					
					// 更新基金的最新公允价值及基金投资剩余金额
					$fund_obj->updateDealInvestAmount ( $fund_id, $fund_last_fair_value, $fund_acount_balance );
					
					// 更新投资人基金收益数据
					$fund_obj->calcInvestorIncomeOfFund ( $fund_id, $fund_last_fair_value, $fund_total_amount, $fund_income_rate_invester );
					
					// 更新投资人所投某基金，按项目计算收益数据
					$fund_obj->calcInvestorIncomeOfFundInvestDeal($fund_id, $fund_income_rate_invester,$deal_income_datas);
					
					// 更新合作人的基金收益数据
					$fund_obj->calcPartnerIncomeOfFund ( $fund_id, $fund_last_fair_value, $fund_total_amount, $fund_income_rate_partner );
					
					// 更新基金所投项目的回报数据
					$fund_obj->calcFundInvestReurn ( $fund_id, $fund_last_fair_value, $fund_total_amount );
				}
			}
			$this->commitTransaction();
			return $this->endResponse ( 'calculate complete', 0 );
		} catch ( Exception $e ) {
			Logs::err ( 'Deal.DealTradeCalculate', LOG_FLAG_ERROR, [
					'开始计算项目各投资基金股权占比及投资回报，执行发生异常，异常原因：',
					$e->getMessage()
			] );
			$this->rollbackTransaction();
			$this->endResponse(null,7);
		}
	}
}