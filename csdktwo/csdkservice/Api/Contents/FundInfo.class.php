<?php

/**
 * 基金详情----Server Api
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Create Time: 2016年9月13日15:31:39
 * +----------------------------------------------------------------------
 * |
 * 
 */
namespace Api\Contents;

use System\Base;
use System\Logs;
define ( 'IMG_DOMAIN', "http://img.cisdaq.com/" );
define ( 'BP_URL', "http://www.cisdaq.com/bp_viewer/get_bp1.php?key=" );
class FundInfo extends Base {
	
	// 身份判断
	public function RaisingUserType($params) {
		$user_id = isset ( $params ['uid'] ) ? trim ( $params ['uid'] ) : NULL;
		$fund_id = isset ( $params ['fund_id'] ) ? trim ( $params ['fund_id'] ) : 0;
		Logs::info ( 'Contents.FundInfo.RaisingUserType', LOG_FLAG_NORMAL, [ 
				'RaisingUserType-募集基金身份判断',
				$params 
		] );
		if (empty ( $params )) {
			return $this->endResponse ( null, 5 );
		}
		$args = [ 
				'user_id' => $user_id,
				'fund_id' => $fund_id 
		];
		
		// 审核通过判断，通过返回1
		$examine = $this->getField ( "select fund.id from cixi_fund_expectant_investor as fund,cixi_user as user  where user.is_effect=1 and user.id=fund.user_id and fund_id=:fund_id  and user_id=:user_id  and status=3", $args );
	  
		// 投资总监身份判断，为投资总监返回2
		$director = $this->getField ( "select id from cixi_user_fund_relation where fund_id=:fund_id and user_id=:user_id and user_type=4", $args );
		  
		if(! empty ( $director )){
			$user_type ='2';
		}elseif ( ! empty ( $examine )){
			$user_type ='1';
		}else{
			$user_type ='0';
		}
		// 待处理准投资人总数
		$plan_number = $this->getField ( "select count(fund.id) as num from cixi_fund_expectant_investor as fund,cixi_user as user  where user.is_effect=1 and user.id=fund.user_id and fund_id=:fund_id  and status=1", [
				'fund_id' => $fund_id
		]);
		$plannum = ! empty ( $plan_number ) ? $plan_number : '0';
		return $this->endResponse ( $user_type ,0);
	}
	
	// 募集基金详情
	public function RaisingFundInfo($params) {
		/**
		 *
		 * @param unknown $user_id
		 *        	用户ID
		 * @param unknown $fund_id
		 *        	基金ID
		 * @param unknown $SOURCE
		 *        	访问来源，默认‘app’，和‘pc’，只有这两个值传其他会sql报错
		 * @return \System\false
		 */
		$user_id = isset ( $params ['uid'] ) ? trim ( $params ['uid'] ) : NULL;
		$fund_id = isset ( $params ['fund_id'] ) ? trim ( $params ['fund_id'] ) : NULL;
		$SOURCE = isset ( $params ['SOURCE'] ) ? trim ( $params ['SOURCE'] ) : 'app';
		Logs::info ( 'Contents.FundInfo.RaisingFundInfo', LOG_FLAG_NORMAL, [ 
				'RaisingFundInfo-募集基金详情',
				$params 
		] );
		 
		if (empty ( $params )) {
			return $this->endResponse ( null, 5 );
		}
		
		$args = [ 
				'fund_id' => $fund_id 
		];
		 
		$sql_fundinfo = "
				SELECT
				fund.id as fund_id,
				fund.name as fund_name,
				short_name,
        		total_amount,
        		invest_min_amount,
				raising_start_date,
				raising_end_date,
				intend_profession,
				pace_of_invest,
				manage_fee,
				invest_type,
				vote_type,
				value_orientation,
				profession_info,
				profession_{$SOURCE}_img as profession_img,
				profession_{$SOURCE}_img_scale as profession_img_scale ,
			 
				invest_philosophy,
				invest_philosophy_{$SOURCE}_img as invest_philosophy_img ,
				invest_philosophy_{$SOURCE}_img_scale as invest_philosophy_img_scale,
				advantage,
				advantage_{$SOURCE}_img as advantage_img,
				advantage_{$SOURCE}_img_scale advantage_img_scale,
				invest_principle,
				invest_principle_{$SOURCE}_img as invest_principle_img,
				invest_principle_{$SOURCE}_img_scale as invest_principle_img_scale,
				decision_process,
				decision_process_{$SOURCE}_img as decision_process_img,
				decision_process_{$SOURCE}_img_scale as decision_process_img_scale,
				exit_channel,
				exit_channel_{$SOURCE}_img as exit_channel_img ,
				exit_channel_{$SOURCE}_img_scale as exit_channel_img_scale,
				income_share,
				income_share_{$SOURCE}_img as income_share_img,
				income_share_{$SOURCE}_img_scale as income_share_img_scale,
				income_rate_calculate,
				income_rate_calculate_{$SOURCE}_img as income_rate_calculate_img,
				income_rate_calculate_{$SOURCE}_img_scale as income_rate_calculate_img_scale,
				risk_type,
				risk_type_{$SOURCE}_img as risk_type_img,
				risk_type_{$SOURCE}_img_scale as risk_type_img_scale,
				version ,
				managers_id
				
				FROM
				cixi_fund as fund,
				cixi_fund_raising as raising
				
				where
				
				fund.id=raising.fund_id
				and fund.fund_period=1
				and fund.status=2
				and fund.is_csdk_fund=1
				and fund.is_delete=1
				and fund.id=:fund_id
				
				";
		
		$result_all = $this->executeQuery ( $sql_fundinfo, $args );
		 
		foreach ( $result_all as $key => $val ) {
			
			$result->fund_id = $val ['fund_id']; // ID
			$result->version = $val ['version']; // 此基金版本号
			$result->fund_name = $val ['fund_name']; // 基金全称 
			$result->manage_fee = $val ['manage_fee']; // 管理费
			$result->pace_of_invest = $val ['pace_of_invest']; // 出资节奏
			$result->invest_type = $val ['invest_type']; // 投资方式
			$result->vote_type = $val ['vote_type']; // 投票方式
			$result->intend_profession = $val ['intend_profession']; // 拟投资行业
			$result->value_orientation = $val ['value_orientation']; // 价值与定位
			$result->raising_start_date = $val ['raising_start_date']; // 募资开始日期
			$result->raising_end_date = $val ['raising_end_date']; // 募资结束日期
			
			$managers_id= $val ['managers_id'];//基金管理公司ID
			
			//pc专用开始
			$result->fund_short_name = $val ['short_name']; // 基金简称
			$result->total_amount = number_format($val ['total_amount']); // 基金规模
			$result->invest_min_amount = number_format($val ['invest_min_amount']); // 最小投资额
			$result->managers_id = $val ['managers_id']; // 基金管理公司ID
			//此投资人是否提交过意向金额，提交过返回金额和申购说明
			$examine = $this->getOne ( "select fund.expect_invest_amount,fund.expect_invest_remark from cixi_fund_expectant_investor as fund,cixi_user as user  where user.is_effect=1 and user.id=fund.user_id and fund_id=:fund_id  and user_id=:user_id ", [
					'user_id' => $user_id ,'fund_id' =>  $val['fund_id'] ] );
			if(!empty($examine)){
				$result->amount  =! empty ( $examine['expect_invest_amount'] ) ? number_format(trim($examine['expect_invest_amount'])) : '';
				$result->remark  =! empty ( $examine['expect_invest_remark'] ) ? $examine['expect_invest_remark'] : '';
			}
			else{
				$result->amount ='';
				$result->remark ='';
			} 
			//pc专用结束
			
			// 行业背景模块
			$profession->desc = empty ($val ['profession_info']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['profession_info']); // 行业背景
			$profession->img = $val ['profession_img']; // 行业背景img
			$profession->scale = is_null ( $val ['profession_img_scale'] ) ? "1" : $val ['profession_img_scale']; // 行业背景img宽高比例
			$result->profession_info = $profession;
			
			// 投资理念模块
			$philosophy->desc =  empty ($val ['invest_philosophy']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['invest_philosophy']); // 投资理念
			$philosophy->img = $val ['invest_philosophy_img']; // 投资理念img
			$philosophy->scale = is_null ( $val ['invest_philosophy_img_scale'] ) ? "1" : $val ['invest_philosophy_img_scale']; // 投资理念img宽高比例
			$result->invest_philosophy = $philosophy;
			
			// 设立方案包含7块内容START////////////////////////////////////////////////////////////////////////
			
			// 核心优势
			if(!empty($val ['advantage'])||!empty($val ['advantage_img'])){
			$advantage->title = "核心优势" ;
			$advantage->desc =  empty ($val ['advantage']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['advantage']);
			$advantage->img = $val ['advantage_img'];  
			$advantage->scale = is_null ( $val ['advantage_img_scale'] ) ? "1" : $val ['advantage_img_scale'];  
			$plans[] = $advantage;
			}
			
			// 投资原则及方式
			if(!empty($val ['invest_principle'])||!empty($val ['invest_principle_img'])){
				$invest_principle->title = "投资原则及方式" ;
				$invest_principle->desc = empty ($val ['invest_principle']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['invest_principle']);
				$invest_principle->img = $val ['invest_principle_img'];
				$invest_principle->scale = is_null ( $val ['invest_principle_img_scale'] ) ? "1" : $val ['invest_principle_img_scale'];
				$plans[] = $invest_principle;
			}
			 
			
			// 决策流程
			if(!empty($val ['decision_process'])||!empty($val ['decision_process_img'])){
				$decision_process->title = "决策流程" ;
				$decision_process->desc = empty ($val ['decision_process']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['decision_process']);
				$decision_process->img = $val ['decision_process_img'];
				$decision_process->scale = is_null ( $val ['decision_process_img_scale'] ) ? "1" : $val ['decision_process_img_scale'];
				$plans[] = $decision_process;
			}
			
			// 退出渠道
			if(!empty($val ['exit_channel'])||!empty($val ['exit_channel_img'])){
				$exit_channel->title = "退出渠道" ;
				$exit_channel->desc = empty ($val ['exit_channel']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['exit_channel']);
				$exit_channel->img = $val ['exit_channel_img'];
				$exit_channel->scale = is_null ( $val ['exit_channel_img_scale'] ) ? "1" : $val ['exit_channel_img_scale'];
				$plans[] = $exit_channel;
			}
			
			// 收益构成及分配
			if(!empty($val ['income_share'])||!empty($val ['income_share_img'])){
				$income_share->title = "收益构成及分配" ;
				$income_share->desc =empty ($val ['income_share']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['income_share']);
				$income_share->img = $val ['income_share_img'];
				$income_share->scale = is_null ( $val ['income_share_img_scale'] ) ? "1" : $val ['income_share_img_scale'];
				$plans[] = $income_share;
			}
			
			// 收益率预测
			if(!empty($val ['income_rate_calculate'])||!empty($val ['income_rate_calculate_img'])){
				$income_rate_calculate->title = "收益率预测" ;
				$income_rate_calculate->desc = empty ($val ['income_rate_calculate']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['income_rate_calculate']);
				$income_rate_calculate->img = $val ['income_rate_calculate_img'];
				$income_rate_calculate->scale = is_null ( $val ['income_rate_calculate_img_scale'] ) ? "1" : $val ['income_rate_calculate_img_scale'];
				$plans[] = $income_rate_calculate;
			}

			// 风险提示
			if(!empty($val ['risk_type'])||!empty($val ['risk_type_img'])){
				$risk_type->title = "风险提示" ;
				$risk_type->desc =empty ($val ['risk_type']) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val ['risk_type']);
				$risk_type->img = $val ['risk_type_img'];
				$risk_type->scale = is_null ( $val ['risk_type_img_scale'] ) ? "1" : $val ['risk_type_img_scale'];
				$plans[] = $risk_type;
			}
			
			$result->plans_obj  = $plans;
			// 设立方案包含7块内容END////////////////////////////////////////////////////////////////////////
			
			// 拟投资项目START///////////////////////////////////////////
			$intend_deal = "
					SELECT
					deal.id as deal_id,
					deal.img_deal_logo as img_deal_logo,
					deal.s_name as deal_name,
					deal.deal_sign as deal_sign,
					deal.period_id as period_id,
					deal.province as province,
					deal.cate_choose as cate_choose
					FROM
					cixi_fund_expectant_deal AS fund,
					cixi_deal as deal
					where
					deal.id = fund.deal_id
					and deal.is_effect=2
					and deal.is_publish=2
					and deal.is_delete=0
					and fund.fund_id=:fund_id
					order by deal.sort asc,  deal.update_time desc
					";
			$deal_all = $this->executeQuery ( $intend_deal, $args );
			foreach ( $deal_all as $key => $val ) {
				$deal_list [$key] ['deal_id'] = $val ['deal_id']; // ID
				$deal_list [$key] ['number'] = $key+1; // ID
				$deal_list [$key] ['deal_name'] = $val ['deal_name']; // 项目简称
				$deal_list [$key] ['img_deal_logo'] = $val ['img_deal_logo']; // 项目小图
				$deal_list [$key] ['deal_sign'] = $val ['deal_sign']; // 项目简介
				// 融资轮次名称
				$deal_list [$key] ['period_name'] = $this->getField ( "select name from cixi_deal_period where id=:period_id", [ 'period_id' => $val ['period_id']]);
				
				// 项目城市
				$deal_list [$key] ['province'] = $this->getField ( "select name from cixi_region_conf where id=:province", [ 'province' => $val ['province']]);
				 
				// 获取行业名称
				$sql_deal_cates = "  select cate.name as cate_name from cixi_deal project, cixi_deal_cate cate, cixi_deal_select_cate selected where selected.deal_id = project.id and selected.cate_id = cate.id and project.id = :deal_id ";
				$result_deal_cates = $this->executeQuery ( $sql_deal_cates, ['deal_id' => $val ['deal_id']]);
				if (! empty ( $result_deal_cates )) {
					foreach ( $result_deal_cates as $k => $v ) {
						$obj_deal_cates [$k] = $v ['cate_name'] . '  ';
					}
				}
				$deal_list [$key] ['deal_cates'] = implode ( $obj_deal_cates );
			}
			$result->intend_deal_list = $deal_list;
			// 拟投资项目END///////////////////////////////////////////
			
			
			// 基金管理公司 START///////////////////////////////////////////
			$company_sql=$this->executeQuery ("select 
					id,
					name,
					legal_person,
					reg_found,
					com_time,
					registration_address
					from
					cixi_fund_managers
					where
					is_del=1 
					and id=:managers_id
					", [ 'managers_id' => $managers_id]);
			if (! empty ( $company_sql )) {
				foreach ( $company_sql as $k => $v ) {
					$fund_company->company_name = $v ['name']  ;//公司名称
					$fund_company->legal_person = $v ['legal_person']  ;//法定代表人
					$fund_company->reg_found =number_format($v ['reg_found'],2)   ;//注册资本
					$fund_company->com_time = $v ['com_time']  ;//成立日期
					$fund_company->registration_address = $v ['registration_address']  ;//注册地址
					
					//公司团队START
						
					$team_sql=$this->executeQuery ("
							
					select 
				    user.user_name,
					team.user_logo,
					fund.position as title,
					fund.brief as brief
					from
					cixi_fund_managers_team as team,
					cixi_user_fund_relation as fund,
					cixi_user as user
 					where
					team.is_del=1
                    and user.id=fund.user_id
					and team.user_id=fund.user_id
					and fund.user_type=4
					and user.is_effect=1
					and user.is_review=1		
					and user.id=team.user_id 
					and managers_id=:managers_id
					and fund.fund_id=:fund_id
					order by fund.id asc
					", [ 'managers_id' => $v ['id'],'fund_id' => $fund_id ]);
					if (! empty ( $team_sql )) {
						foreach ( $team_sql as $k => $v ) {
							$team[$k]->user_name = $v ['user_name']  ;//总监名称
							$team[$k]->user_logo = $v ['user_logo']  ;//头像
							$team[$k]->title = $v ['title']  ;//总监职位
							$team[$k]->brief = $v ['brief']  ;//总监简介
						}
						$fund_company->team_list = $team;
					}
					
					//公司团队END
					
				}
			}
			
			$result->fund_company_obj = $fund_company;
			// 基金管理公司END///////////////////////////////////////////
			
			
			// 基金档案 START///////////////////////////////////////////
			
			$attachment_sql=$this->executeQuery ("
					select
					id,
					title,
					attachment,
					type
					FROM 
					cixi_fund_attachment
					WHERE   
					is_del=1 
					and fund_id=:fund_id
					order by publish_time desc ,id desc
					limit 	1000
					", $args);
			if (! empty ( $attachment_sql )) {
				foreach ( $attachment_sql as $k => $v ) {
					$attachment[$k]->att_id = $v ['id']  ;//主键
					$attachment[$k]->att_title = $v ['title']  ;//信息标题					
					$attachment[$k]->att_type = $v ['type']  ;//文件类型 1图片 2PDF 3其他 
					if($v ['type']==1){
						$attachment[$k] -> att_url = is_null($v['attachment']) ? "" : IMG_DOMAIN.$v['attachment'];
					}
					else
					{
						$attachment[$k] -> att_url = is_null($v['attachment']) ? "" :BP_URL. $v['attachment'];
					}
				}
				$result->attachment_list = $attachment;
			}

			// 基金档案   END///////////////////////////////////////////
			
			
		}
		
		return $this->endResponse ( $result, 0 );
	}
}

?>