<?php

/**
 * 发送募集基金投资意向金额，投资总监审核操作等\意向投资人列表 ExpectantInvestor----Server Api
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Create Time: 2016年9月19日17:23:15
 * +----------------------------------------------------------------------
 * |
 * 
 */
namespace Api\Contents;

use System\Base;
use System\Logs;
use Api\Common\Sms;


class ExpectantInvestor extends Base {
	
	// 发送募集基金投资意向金额，投资总监审核操作等
	public function InvestmentAmount($params) {
		$user_id = isset ( $params ['uid'] ) ? trim ( $params ['uid'] ) : NULL;
		$fund_id = isset ( $params ['fund_id'] ) ? trim ( $params ['fund_id'] ) : NULL;
		$expect_invest_amount = isset ( $params ['amount'] ) ? trim ( $params ['amount'] ) : NULL;
		$expect_invest_remark = isset ( $params ['remark'] ) ? trim ( $params ['remark'] ) : NULL;
		$status = isset( $params ['turn_type'] ) ? trim ( $params ['turn_type'] ) : NULL; 
		Logs::info ( 'Contents.ExpectantInvestor.InvestmentAmount', LOG_FLAG_NORMAL, [ 
				'InvestmentAmount-拟投金额/总监审核意向投资人',
				$params 
		] );
		if (empty ( $params )) {
			return $this->endResponse ( null, 5 );
		}
		$args = [ 
				'user_id' => $user_id,
				'fund_id' => $fund_id 
		];
		
		if (! is_null ( $expect_invest_amount ) || ! is_null ( $expect_invest_remark )|| ! is_null ( $status )) {
 
			// 判断是否第一次提交数据
			$examine = $this->getOne ( "select fund.id,fund_name.short_name,fund.status as fund_status,fund.actual_invest_confirm as confirm  from cixi_fund_expectant_investor as fund,cixi_user as user,cixi_fund as fund_name  where fund_name.id=fund.fund_id and user.is_effect=1 and user.id=fund.user_id and fund_id=:fund_id  and user_id=:user_id ", $args );
			// 已存在
			if ($examine) {
				if ( $examine['confirm']=='2') {
					return $this->endResponse ( null, 14 );
				}
				$para_value = array ();
				
				
				// 操作
				if (! is_null ( $status )) {
					$update_fields .= "status = :status,";
					$para_value ['status'] = $status;
				}else{
					// 投资金额
					if (! is_null ( $expect_invest_amount )) {
						$update_fields .= "expect_invest_amount = :expect_invest_amount,";
						$para_value ['expect_invest_amount'] = $expect_invest_amount;
					}
					
					// 认购说明
					if (! is_null ( $expect_invest_remark )) {
						$update_fields .= "expect_invest_remark = :expect_invest_remark,";
						$para_value ['expect_invest_remark'] = $expect_invest_remark;
					}
				}
				//用户ID
				$update_fields = substr ( $update_fields, 0, - 1 );
				$where_fields = "user_id=:user_id";
				$para_value ['user_id'] = $user_id;
				$sql = "update cixi_fund_expectant_investor set  " . $update_fields . "  where fund_id=$fund_id and  " . $where_fields;
				$this->executeNonQuery ( $sql, $para_value ); 
				// 投资总监审核操作， 2：已驳回 3：已通过
				if (! is_null ( $status )) {
					
					
					//如果审核通过，检查此投资人会员认证状态，如果未认证变更认证，发送短信和站内信。
					if($status=='3'){
						$user_review= $this->getOne ( "select  mobile,is_review from cixi_user where is_effect=1 and   id=:user_id ", ['user_id' => $user_id] );  
						 if($user_review['is_review']!='1'){ 
						 	
						 	//发送审核站内信
						 	$sql_notify = "insert into cixi_user_notify (user_id,log_info,log_time,url) values (:user_id,:log_info,:log_time,:url)";
						 	$para_notify = [ 
						 	 'user_id' => $user_id,'log_info' => '恭喜您已经完成认证！可完整体验磁斯达克为您带来的专业服务。',
						 	 'log_time' => time(),'url' => ''];  
						    $this->executeNonQuery ( $sql_notify, $para_notify );  
						    
						    // 更新会员状态
						    $sql_review = "update cixi_user set is_review=1  where   id=:user_id";
						    $this->executeNonQuery ( $sql_review, ['user_id' => $user_id] );
						    
						    // 会员认证通过发送短信
						    $params = array(
						    		"mobile"    => $user_review['mobile'],
						    		"content"   =>  "恭喜您已经完成认证！可完整体验磁斯达克为您带来的专业服务。",
						    		"type"      => '31',
						    		"source"    => 'app',
						    );
						    $SMS = new SMS();
						    $SMS->serversendSms($params);
						 } 
						 //如果数据库已经是通过的，再操作就不会触发短信和站内信///防止刷操作。
						 if($examine['fund_status']!='3'){
						 //发送意向申购站内信
						 $sql_notify = "insert into cixi_user_notify (user_id,log_info,log_time,url) values (:user_id,:log_info,:log_time,:url)";
						 $para_notify = [
						 		'user_id' => $user_id,'log_info' => '尊敬的投资人，您对'.$examine['short_name'].'基金发起的“意向申购”申请已经通过！',
						 		'log_time' => time(),'url' => ''];
						 $this->executeNonQuery ( $sql_notify, $para_notify );
						 
						 // 发送意向申购短信
						 $params = array(
						 		"mobile"    => $user_review['mobile'],
						 		"content"   =>  '尊敬的投资人，您对'.$examine['short_name'].'基金发起的“意向申购”申请已经通过！',
						 		"type"      => '32',
						 		"source"    => 'app',
						 		"skip"    => '1',
						 );
						 $SMS = new SMS(); 
						 $SMS->serversendSms($params); 
						 }
					}
					//驳回
					elseif ($status=='2'){
						//如果数据库已经是通过的，再操作就不会触发短信和站内信///防止刷操作。
						if($examine['fund_status']!='2'){
							//发送意向申购站内信
							$sql_notify = "insert into cixi_user_notify (user_id,log_info,log_time,url) values (:user_id,:log_info,:log_time,:url)";
							$para_notify = [
									'user_id' => $user_id,'log_info' => '尊敬的投资人，您对'.$examine['short_name'].'基金发起的“意向申购”申请已被驳回！',
									'log_time' => time(),'url' => ''];
							$this->executeNonQuery ( $sql_notify, $para_notify ); 
						}
					}
				}
				
				
			}  
			
			
			// 第一次提交
            else {
				$sql = "INSERT INTO cixi_fund_expectant_investor(`fund_id`,`user_id`,`expect_invest_amount`,`expect_invest_remark`) VALUES(:fund_id,:user_id,:expect_invest_amount,:expect_invest_remark)";
				$para = [ 
						'fund_id' => $fund_id,
						'user_id' => $user_id,
						'expect_invest_amount' => $expect_invest_amount,
						'expect_invest_remark' => $expect_invest_remark 
				];
				$this->executeNonQuery ( $sql, $para );
			}
			
			return $this->endResponse ( 0, 0 );
		} else {
			return $this->endResponse ( 0, 1001 );//失败
		}
	}
	
	//意向投资人列表 
	public function ExpectantInvestorList($params){
		$user_id = isset ( $params ['uid'] ) ? trim ( $params ['uid'] ) : NULL;
		$fund_id = isset ( $params ['fund_id'] ) ? trim ( $params ['fund_id'] ) : NULL;
		$search  = isset ( $params ['search'] ) ? trim ( $params ['search'] ) : NULL;
		$page_num  = isset ( $params ['page'] ) ? trim ( $params ['page'] ) : 1;
		define("PAGE_SIZE", 1000);
		$page_offset= PAGE_SIZE*$page_num - PAGE_SIZE;
		$page_rows 	= PAGE_SIZE;
		Logs::info ( 'Contents.ExpectantInvestor.ExpectantInvestorList', LOG_FLAG_NORMAL, [
		'ExpectantInvestorList-意向投资人列表 ',
		$params
		] );
		if (empty ( $params )) {
			return $this->endResponse ( null, 5 );
		}
		$args = [
				'user_id' => $user_id,
				'fund_id' => $fund_id
		];
		// 投资总监身份判断，为投资总监返回2
		$director = $this->getField ( "select id from cixi_user_fund_relation where fund_id=:fund_id and user_id=:user_id and user_type=4", $args);
		if( ! empty($director) ){
			$sql_list="
					select
					user.id as user_id,
					user.user_name,
					user.mobile,
					fund.status as turn_type
					from
					cixi_fund_expectant_investor as fund,
					cixi_user as user
					where
					user.id = fund.user_id
					and  user.is_effect=1
					and  fund.fund_id=:fund_id 
					";
			if($search){
			$sql_search=" and (user_name like '%{$search}%'  or mobile like '%{$search}%' )";}
			else{$sql_search="";}
			$sql_order=" order by fund.status asc,fund.update_time desc,fund.create_time desc ,fund.id desc";
			$sql_limit=" limit $page_offset, $page_rows";
			$sql=$sql_list.$sql_search.$sql_order.$sql_limit; 
			$list_result = $this->executeQuery ($sql, ['fund_id' => $fund_id] );
		 
			if(!empty($list_result)){
				/* foreach ($list_result as $key=>$val){ 
					$fund_list['user_id']= is_null($val['user_id']) ? ""		: $val['user_id'];
					$fund_list['user_name']= is_null($val['user_name']) ? ""		: $val['user_name'];
					$fund_list['mobile'] = is_null($val['mobile']) ? ""		: $val['mobile'];
					$fund_list['turn_type'] = is_null($val['turn_type']) ? ""		: $val['turn_type']; 
					$fund_list2->fund_list[]=$fund_list;
				} */ 
				return $this->endResponse ( $list_result, 0 );
			}
			return $this->endResponse ( null, 41 );
		}
		else{
			return $this->endResponse ( null, 42 );
		}
	}
	
}
?>