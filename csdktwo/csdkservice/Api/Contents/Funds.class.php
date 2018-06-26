<?php

/**
 * 我的基金列表----Server Api
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Create Time: 2016年9月12日17:44:14
 * +----------------------------------------------------------------------
 * |
 * 
 */
namespace Api\Contents;

use System\Base;
use System\Logs;

class Funds extends Base {
	public function FundsList($params) {
		/**
		 *
		 * @param unknown $user_id
		 *        	用户ID
		 * @param unknown $label
		 *        	页面单刷标记，1为募集基金，2为投资基金，3为发起基金，4为管理基金 ，列表
		 * @return \System\false
		 */
		$user_id = isset ( $params ['uid'] ) ? trim ( $params ['uid'] ) : NULL;
		$label = isset ( $params ['label'] ) ? trim ( $params ['label'] ) : 0;
		Logs::info ( 'Contents.Funds.FundsList', LOG_FLAG_NORMAL, [ 
				'FundsList-我的基金列表',
				$params 
		] );
		if (empty ( $params )) {
			return $this->endResponse ( null, 5 );
		}
		$user_table = $this->getMainDbTablePrefix () . "user";
		$args = [ 
				'id' => $user_id 
		];
		$sql_is_review = "select is_review from {$user_table} where id=:id ";
		$user_info = $this->getOne ( $sql_is_review, $args );
		
		// 募资阶段基金列表，注册会员全部可见
		$sql_raising = "
        		SELECT 
        		id as fund_id,
        		short_name,
        		total_amount,
        		deadline,
                invest_min_amount as user_type,
                establish_date  as create_time
        		FROM
        		cixi_fund as fund
        		WHERE
        		fund_period=1 
        		and is_csdk_fund=1
        		and is_delete=1
        		and status=2
        		";
		
		//合并子集
		$sql_union=" UNION "; 
		
		
		// 投资阶段的基金列表，参与投资的认证会员可见
		$sql_invest="
			SELECT
        	fund.id as fund_id,
        	fund.short_name,
        	fund.total_amount,
        	fund.deadline,
        	userfund.user_type	,
            fund.establish_date  as create_time
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id
        	and fund.fund_period=2		
		    and fund.is_delete=1
		    and fund.status=2
		    and fund.is_csdk_fund=1
		    and userfund.user_id=:id
				";
		
		//创建时间倒序
		$sql_order="order by   create_time desc ,fund_id desc ";
		
		if ($user_info['is_review'] == 1) {
			if($label>'1'){
				$sql_funds= $sql_invest. $sql_order;
			}
			elseif ($label=='1'){
				$sql_funds=$sql_raising  . $sql_order;
			}
			else{
				$sql_funds=$sql_raising . $sql_union . $sql_invest. $sql_order;
			}
 
		}
		else {
			$sql_funds=$sql_raising  . $sql_order;
		}
 
		$funds_result = $this->executeQuery ($sql_funds, $args );
 
		// 返回结果集合
		if (count ( $funds_result ) > 0) {
 
			$funds_raising=array();
			$funds_invest=array();
			$funds_assist=array();
			$funds_manage=array();
			foreach ($funds_result as $key=>$val){
				$fund_list=array();
				$fund_list['fund_id'] = is_null($val['fund_id']) ? ""		: $val['fund_id'];
				$fund_list['fund_short_name'] = is_null($val['short_name']) ? ""		: $val['short_name'];
				$fund_list['total_amount'] = is_null(number_format($val['total_amount'])) ? ""		: number_format($val['total_amount']);
				$fund_list['deadline'] = is_null($val['deadline']) ? ""		: $val['deadline'];
				$fund_list['invest_min_amount'] = is_null(number_format($val['user_type'])) ? ""		: number_format($val['user_type']);
				$fund_list['user_type']='0';
				
				//此投资人是否提交过意向金额，提交过返回金额和申购说明
				$examine = $this->getOne ( "select fund.expect_invest_amount,fund.expect_invest_remark,fund.actual_invest_confirm as confirm,fund.status as status from cixi_fund_expectant_investor as fund,cixi_user as user  where user.is_effect=1 and user.id=fund.user_id and fund_id=:fund_id  and user_id=:user_id ", [
						'user_id' => $user_id ,'fund_id' =>  $val['fund_id'] ] );
				//var_dump($examine);
				if(!empty($examine)){
					$fund_list['amount']  =! empty ( $examine['expect_invest_amount'] ) ? number_format(trim($examine['expect_invest_amount'])) : '';
					$fund_list['remark']  =! empty ( $examine['expect_invest_remark'] ) ? $examine['expect_invest_remark'] : '';
					$fund_list['confirm']  =! empty ( $examine['confirm'] ) ? $examine['confirm'] : 1;
						
					//审核通过
					if($examine['status']=='3'){
						$fund_list['user_type']='1';
					}
				}
				else{
					$fund_list['amount']  ='';
					$fund_list['remark']  ='';
					$fund_list['confirm']  ='1';
				}
				
				// 投资总监身份判断，为投资总监返回2
				$director = $this->getField ( "select id from cixi_user_fund_relation where fund_id=:fund_id and user_id=:user_id and user_type=4", [
						'user_id' => $user_id,
						'fund_id' => $val['fund_id'],	]); 
				if( ! empty($director) ){
					// 待处理准投资人总数
					$plan_number = $this->getField ( "select count(fund.id) as num from cixi_fund_expectant_investor as fund,cixi_user as user  where user.is_effect=1 and user.id=fund.user_id and fund_id=:fund_id  and status=1", [
							'fund_id' =>  $val['fund_id'] ]);
					$fund_list['plannum']  =! empty ( $plan_number ) ? $plan_number : '0';
					$fund_list['director']='1';
					$fund_list['user_type']='2';
				} 
				else{
					$fund_list['plannum']='0';
					$fund_list['director']='0';
				} 
				
				
			 
				 if($val['user_type']=='1'){
				 	$funds_invest['label']='2';
				 	$funds_invest['title']='我投资的';
				 	$funds_invest['fund_list'][]=$fund_list;
				 }
				 elseif($val['user_type']=='3'){
				 	$funds_assist['label']='3';
				 	$funds_assist['title']='协助发起';
				 	$funds_assist['fund_list'][]=$fund_list;
				 }
				 elseif($val['user_type']=='4'){
				 	$funds_manage['label']='4';
				 	$funds_manage['title']='我管理的';
				 	$funds_manage['fund_list'][]=$fund_list;
				 }
				 else{
				 		$funds_raising['label']='1';
				 		$funds_raising['title']='意向投资';
				 		$funds_raising['fund_list'][]=$fund_list;
				 }
 
			}
			if (!empty ($funds_raising)&&($label=='1' or $label=="0")) {
				$funds_list[]=$funds_raising; 
			}
			if (!empty ( $funds_invest )&&($label=='2' or $label=="0")) {
				$funds_list[]=$funds_invest;
			}
			if (!empty ( $funds_assist )&&($label=='3' or $label=="0")) {
				$funds_list[]=$funds_assist;
			}
			if (!empty ( $funds_manage )&&($label=='4' or $label=="0")) {
				$funds_list[]=$funds_manage;
			}
 
			
			return $this->endResponse ($funds_list, 0);
  
		} else {
			return $this->endResponse ( null, 31 );
		}
	}
}

?>