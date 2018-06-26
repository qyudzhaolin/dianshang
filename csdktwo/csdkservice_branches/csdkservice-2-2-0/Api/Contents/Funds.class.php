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
 * | Create Time: 2016年9月8日11:53:08
 * +----------------------------------------------------------------------
 * |
 * 
 */
namespace Api\Contents;

use System\Base;
use System\Logs;

class Funds extends Base
{

    public function FundsList($params)
    {
    	/**
    	 * @param unknown $user_id 用户ID
    	 * @param unknown $label 页面单刷标记，1为募集基金，2为投资基金，3为发起基金，4为管理基金 ，列表
    	 * @return \System\false
    	 */
        $user_id = isset($params['uid']) ? trim($params['uid']) : NULL;
        $label = isset($params['label']) ? trim($params['label']) : 0;
        Logs::info('Contents.Funds.FundsList', LOG_FLAG_NORMAL, [
            'FundsList-我的基金列表',
            $params
        ]);
        if (empty($params)) {
            return $this->endResponse(null, 5);
        }
        $user_table = $this->getMainDbTablePrefix() . "user";
        $args = ['id' => $user_id];
        $sql_is_review = "select is_review from {$user_table} where id=:id ";
        $user_info = $this->getOne($sql_is_review, $args);
        
        //募资基金列表，注册会员全部可见
        $sql_raising="
        		SELECT 
        		id as fund_id,
        		short_name,
        		total_amount,
        		invest_min_amount,
        		deadline
        		FROM
        		cixi_fund
        		WHERE
        		fund_period=1 
        		and is_csdk_fund=1
        		and is_delete=1
        		and status=2
        		order by id desc
        		";
        $raising_result = $this->executeQuery($sql_raising, NULL);
        
        //认证投资人可见的三个列表
        if ($user_info['is_review'] == 1) {
        	$sql_investor="
			SELECT
        	fund.id as fund_id,
        	fund.short_name,
        	fund.total_amount,
        	fund.deadline,
        	userfund.user_type		
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
		    order by userfund.create_time desc,fund.establish_date desc,fund.id desc ";
        	$investor_result = $this->executeQuery($sql_investor, $args);
             //	var_dump($investor_result);
        	//var_dump($investor_result);die;
        	if (count($investor_result) > 0 ) {
        		$invest_result=array();
        		$assist_result = array();
        		$manage_result = array();
        		//投资阶段基金
        		foreach ($investor_result as $key=>$val){
        			$investor_sign='1';//基金结果标记，1代表有值
        			$fund_list['fund_id'] = is_null($val['fund_id']) ? ""		: $val['fund_id'];
        			$fund_list['fund_name'] = is_null($val['short_name']) ? ""		: $val['short_name'];
        			$fund_list['total_amount'] = is_null(number_format($val['total_amount'])) ? ""		: number_format($val['total_amount']);
        			$fund_list['deadline'] = is_null($val['deadline']) ? ""		: $val['deadline'];
        			//我投资的基金
        			if($val['user_type'] == 1){
         				$fund_list['label']='2';
        				array_push($invest_result, $fund_list);
        			}
        			
        			//我发起的基金
        			elseif($val['user_type'] == 3){
        				$fund_list['label']='3';
        				array_push($assist_result, $fund_list);
        			}
        			//我管理的基金
        			else{
        				$fund_list['label']='4';
        				array_push($manage_result, $fund_list);
        			}
        			
        		} 
        	 
        	 
        	}
        	else{
        		$investor_sign='0';//基金结果标记，0代表无值
        	}
        }
        else{
        	$investor_sign='0';//基金结果标记，0代表无值
        }
        
       
     
        //返回结果集合
        if (count($raising_result) > 0 or $investor_sign=='1') {
        	foreach ($raising_result as $k=>$v){
        		$raising_result[$k]['label'] ='1';
        	    $raising_result[$k]['total_amount']= is_null(number_format($v['total_amount'])) ? ""		: number_format($v['total_amount']);
        	    $raising_result[$k]['invest_min_amount']= is_null(number_format($v['invest_min_amount'])) ? ""		: number_format($v['invest_min_amount']);
        	}
        	//第一次访问，全部返回
        	if($label=='0'){
        	return $this->endResponse(
        			
        			array(
        				
        			//募资阶段基金
        			'raising_fund'=>$raising_result,
        			//我投资的基金
        			'invest_fund'=>$invest_result,
        		    //我发起的基金
        			'assist_fund'=>$assist_result,
        			//我管理的基金
        			'manage_fund'=>$manage_result,
        			)
        			,0);
        	}
        	//募资阶段基金单刷
        	elseif($label=='1'){
        		return $this->endResponse(
        				array(
        						
        						'raising_fund'=>$raising_result,
        				)
        				,0);
        	}
        	//我投资的基金单刷
        	elseif($label=='2'){
        		return $this->endResponse(
        				array(
        	
        						'invest_fund'=>$invest_result,
        				)
        				,0);
        	}
        	//我发起的基金单刷
        	elseif($label=='3'){
        		return $this->endResponse(
        				array(
        	
        						'assist_fund'=>$assist_result,
        				)
        				,0);
        	}
        	//我管理的基金单刷
        	elseif($label=='4'){
        		return $this->endResponse(
        				array(
        	
        						'manage_fund'=>$manage_result,
        				)
        				,0);
        	}
        	else{
        		return $this->endResponse(null,32);
        	}
        }
        else{
        	return $this->endResponse(null,31);
        }
        
        
       
    }
 
	 
 
}

?>