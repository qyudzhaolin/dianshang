<?php

/**
 * 我的收益首页列表和基金投资收益详情----Server Api
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */
namespace Api\Contents;

use System\Base;
use System\Logs;

class Profit extends Base
{

    public function ProfitList($params)
    {
        $user_id = isset($params['uid']) ? trim($params['uid']) : NULL;
        Logs::info('Contents.Profit.ProfitList', LOG_FLAG_NORMAL, [
            'ProfitList-我的收益首页',
            $params
        ]);
        if (empty($params)) {
            return $this->endResponse(null, 5);
        }
        $user_table = $this->getMainDbTablePrefix() . "user";
        $args = [
            'id' => $user_id
        ];
        $sql_is_review = "select is_review from {$user_table} where id= :id ";
        $user_info = $this->getOne($sql_is_review, $args);
        if ($user_info['is_review'] == 1) {
            $profit_sql = "
			select
			fund.id as fund_id,
		    fund.short_name as short_name,
		    fund.total_amount as total_amount,	
			fund.establish_date as establish_date,				
			fund.fund_income_rate_partner as fund_income_rate_partner,				
		    profit.user_type as user_type,
		    profit.investor_amount as investor_amount,
    		profit.fund_income as fund_income,
			profit.investor_rate as investor_rate,
			profit.create_time as create_time
		    from
		    cixi_user_fund_relation as profit, 
		    cixi_fund as fund
		    where
            profit.fund_id=fund.id 
            and fund.is_csdk_fund=1 
            and fund.status=2 
           	and fund.fund_period=2
            and fund.is_delete=1 
            and profit.user_type<>4  
			and profit.user_id=:id 
			order by profit.user_type asc,profit.fund_income desc,profit.investor_amount desc";
            $result = $this->executeQuery($profit_sql, $args);
            $profit_carry = ""; // 渠道合伙人标记
            $profit_investor = ""; // 投资人标记
            if (count($result) > 0) {
                foreach ($result as $key => $val) {
                    // 账面预期总收益,carry为负不参与计算
                    $expect_profit += number_format($val['fund_income'],0)<0&&$val['user_type'] == 3?'0':number_format($val['fund_income'],0);
                    //无四舍五入总收益,carry为负不参与计算
                     $expect_profit_db +=$val['fund_income']<0&&$val['user_type'] == 3?'0':$val['fund_income'];
                    // 出资份额
                    $sum_amount += $val['investor_amount'];
                    
                    if ($val['user_type'] == 1) {
                        $profit_investor = '1';
                        $sum_investor_profit += number_format($val['fund_income'],0);
                        // 单个基金的收益率
                        $result[$key]['profit_rate'] = number_format(($val['fund_income'] / $val['investor_amount']) * 100, 2);
                    }
                    
                    if ($val['user_type'] == 3) {
                        $profit_carry = '2';
                        //carry为负不参与计算
                        $sum_carry_profit += number_format($val['fund_income'],0)<0?'0':number_format($val['fund_income'],0);
                        // 募集金额占比
                        $result[$key]['assist_rate'] = $this->getField("select sum(investor_rate) from cixi_user_fund_relation where user_type=1 and partner_user_id=:uid and fund_id=:fund_id", [
                            'uid' => $user_id,
                            'fund_id' => $val['fund_id']
                        ]);
                        // 募集金额
                        $result[$key]['sum_investor_amount'] = $this->getField("select sum(investor_amount) from cixi_user_fund_relation where user_type=1 and partner_user_id=:uid and fund_id=:fund_id", [
                            'uid' => $user_id,
                            'fund_id' => $val['fund_id']
                        ]);
                        
                    }
					
                    //carry小于等于0显示-，投资基金等于0显示-
					if((number_format($val['fund_income'], 0)<=0&&$val['user_type'] == 3) or ($val['user_type'] == 1&&number_format($val['fund_income'], 0)==0)){
                        $result[$key]['fund_income']='-';
                        }else{
                        $result[$key]['fund_income']=number_format($val['fund_income'], 0);
                        }
                    
                }
                $profit_type = $profit_investor + $profit_carry;
                // 收益计算方式
                if ($profit_type == '1') {
                    $compute_formula = "基金收益计算公式（以下均为税前收益）\n基金投资收益（不含本金）：\n指在扣除基金出资份额后所获得的投资收益，具体收益由您的基金初始出资份额占比决定。\n基金投资收益率＝基金投资收益（不含本金）／基金出资份额（本金）。";
                } elseif ($profit_type == '2') {
                    $compute_formula = "基金收益计算公式（以下均为税前收益）\n基金carry分成收益：\n指基金获得投资收益后分给您协助募资的激励，具体分成比例由基金管理公司决定。";
                } else {
                    $compute_formula = "基金收益计算公式（以下均为税前收益）\n基金账面总收益 ＝ 基金投资收益 + 基金carry分成收益\n基金投资收益（不含本金）：\n指在扣除基金出资份额后所获得的投资收益，具体收益由您的基金初始出资份额占比决定。\n基金carry分成收益：\n指基金获得投资收益后分给您协助募资的激励，具体分成比例由基金管理公司决定。";
                }
                
                return $this->endResponse(array(
                    'profit_list' => $result,
                    // 账面预期总收益
                    'expect_profit' => $expect_profit=='0' ? '-' : number_format($expect_profit, 0),
                    // 身份标示，1投资人，2合伙人，3投资+合伙人
                    'profit_type' => $profit_type,
                    // 收益计算方式文字
                    'compute_formula' => $compute_formula,
                    // 总收益率
                    'expect_rate' => $expect_profit=='0' ? '-' : number_format(($expect_profit_db / $sum_amount) * 100, 2),
                    // 总出资份额
                    'sum_amount' => is_null($sum_amount) ? '0' : number_format($sum_amount, 0),
                    // 基金出资份额税前收益
                    'sum_investor_profit' =>$sum_investor_profit=='0' ? '-' : number_format($sum_investor_profit, 0),
                    // 基金Carry收益
                    'sum_carry_profit' => $sum_carry_profit<='0' ? '-' : number_format($sum_carry_profit, 0)
                ), 0);
            } else {
                return $this->endResponse(null, 20);
            }
        } else {
            // 审核未通过收益为0，返回错误20未收益标记
            return $this->endResponse(null, 20);
        }
    }
 
	public function ProfitInfo($params){
		$user_id = isset ( $params ['uid'] ) ? trim ( $params ['uid'] ) : NULL;
		$fund_id = isset ( $params ['fund_id'] ) ? trim ( $params ['fund_id'] ) : NULL;
		if (empty ( $params )) {
			return $this->endResponse ( null, 5 );
		}
		$args_info = [
				'user_id' => $user_id,
				'fund_id' => $fund_id
		];
		$sum_investor_profit=$this->getField("select sum(fund_income) from cixi_user_fund_deal_relation where user_type=1 and user_id=:uid and fund_id=:fund_id",['uid'=>$user_id,'fund_id' => $fund_id]);
		 
		$info_sql="
				select 
				deal.id as deal_id,
				info.fund_income as fund_income,
				deal.s_name as deal_name
				
				from
				cixi_user_fund_deal_relation as info,
				cixi_deal as deal
				
				where
				info.user_id=:user_id 
				and info.fund_id=:fund_id
				and info.deal_id=deal.id
				
				order by info.fund_income desc, info.create_time desc
				";
		$result = $this->executeQuery ( $info_sql, $args_info );
		if (count ( $result ) > 0) {
			foreach ( $result as $key => $val ) {
					//此项目收益金额占比
					$result[$key]['assist_rate']=number_format(($val['fund_income']/abs($sum_investor_profit))*100,2) ;
					if(number_format($val['fund_income'], 0)==0){
						$result[$key]['fund_income']='-';
					}else{
						$result[$key]['fund_income']=number_format($val['fund_income'], 0);
					}
			}
			return  $this->endResponse(
					array(
                    'deal_list' => $result,
                    // 基金出资份额税前收益
                    'sum_investor_profit' => is_null($sum_investor_profit) ? '0' : number_format($sum_investor_profit, 2)
					),0);
		}
		else{
			return $this->endResponse ( null, 21 );
		}
		
	}
 
}

?>