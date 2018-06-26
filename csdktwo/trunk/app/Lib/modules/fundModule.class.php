<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class fundModule extends BaseModule
{

    /*
     * 募集基金列表
     */
    public function raising_list()
    {
        $user_id = $GLOBALS['user_info']['id'];
        $params = array(
            "uid" => $user_id,
            "label" => 1
        );
        $result = request_service_api("Contents.Funds.FundsList", $params);
        $raising_list = $result['response'][0]['fund_list'];
        if (! empty($raising_list)) {
            foreach ($raising_list as $k => $v) {
                $sql_pc = "
        		 		select 
        		 		fund.summary as summary,
        		 		raising.raising_start_date,
        		 		raising.raising_end_date,
        		 		managers.short_name as managers_name
        		 		from
        		 		cixi_fund as fund,
        		 		cixi_fund_raising as raising,
        		 		cixi_fund_managers as managers
        		 		where
        		 		fund.id=raising.fund_id
        		 		and fund.managers_id=managers.id
        		 		and fund.id={$v['fund_id']}
        		 		";
                $pc_other = $GLOBALS['db']->getRow($sql_pc);
                $raising_list[$k]['amount'] = empty($v['amount']) ? "" : $v['amount'];
                $raising_list[$k]['summary'] = $pc_other['summary'];
                $raising_list[$k]['managers_name'] = $pc_other['managers_name'];
                $raising_list[$k]['start_date'] = to_date($pc_other['raising_start_date'], "Y-m-d");
                $raising_list[$k]['end_date'] = to_date($pc_other['raising_end_date'], "Y-m-d");
            }
        }
        // var_dump($raising_list);
        $GLOBALS['tmpl']->assign("raising_list", $raising_list);
        $GLOBALS['tmpl']->assign("page_title", "意向基金");
        $GLOBALS['tmpl']->assign("pageType", PAGE_MENU_RAISING);
        $GLOBALS['tmpl']->assign("sideType", SIDE_MENU_RAISING);
        $GLOBALS['tmpl']->display("fund_raising_list.html");
    }

    /*
     * 募集基金列表->发送投资意向
     */
    public function send_investment_intent()
    {
        $user_id = $GLOBALS['user_info']['id'];
        
        if ($user_id) {
            $fid    = isset($_POST['fid']) ? intval($_POST['fid']) : 0;
            $amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
            $remark = isset($_POST['remark']) ? trim($_POST['remark']) : 0;
            $invest_min_amount = $GLOBALS['db']->getOne("select invest_min_amount from " . DB_PREFIX . "fund where id = {$fid}");
            
            if (empty($fid)) {
                $res['status']  = 1;
                $res['info']    = "Error:参数丢失";
            } elseif (! $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "fund where id = {$fid} and fund_period = 1 and is_csdk_fund = 1 and is_delete = 1 and status = 2")) {
                $res['status']  = 1;
                $res['info']    = "Error:非法操作";
            } elseif (! empty($amount) && $invest_min_amount > $amount) {
                $res['status']  = 2;
                $res['info']    = "拟认购金额必须大于起投金额";
            } else {
                
                $params = array(
                    "uid"       => $user_id,
                    "fund_id"   => $fid,
                    "amount"    => $amount,
                    "remark"    => $remark
                );
                $result = request_service_api("Contents.ExpectantInvestor.InvestmentAmount", $params);
                
                if ($result['status'] == 0) {
                    $res['status']  = 0;
                    $res['amount']  = empty($amount) ? "" : number_format($amount);
                    $res['remark']  = $remark;
                    $res['info']    = "success";
                } else {
                    $res['status']  = 5;
                    $res['info']    = $result['message'];
                }
            }
        } else {
            $res['status']  = 4;
            $res['info']    = "Error:用户登录状态丢失";
        }
        
        ajax_return($res);
    }

    /*
     * 募集基金详情页
     */
    public function raising_details()
    {
        $fund_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        if(empty($fund_id)){
            // 404
            app_redirect(url("error#index"));
        }
        
        $user_id    = (int) $GLOBALS['user_info']['id'];
        $params     = array(
            "uid"       => $user_id,
            "fund_id"   => $fund_id,
            "SOURCE"    => 'pc'
        );
        
        $result = request_service_api('Contents.FundInfo.RaisingFundInfo', $params);
        if ($result['status'] == 0) {
            // 判断审核通过与不通过
            $params = array(
                "uid" => $user_id,
                "fund_id" => $fund_id
            );
            $state = request_service_api('Contents.FundInfo.RaisingUserType', $params);
            if ($state['response'] == 2) {
                $GLOBALS['tmpl']->assign("state", 2);
            } elseif ($state['response'] == 1) {
                $GLOBALS['tmpl']->assign("state", 1);
            } else {
                $GLOBALS['tmpl']->assign("state", 0);
            }
            $response = $result['response'];
            // 行业背景
            $profession_info = $response['profession_info'];
            // 投资理念
            $invest_philosophy = $response['invest_philosophy'];
            // 标的项目
            $intend_deal_list = $response['intend_deal_list'];
            // 基金管理人
            $fund_company_obj = $response['fund_company_obj'];
            // 核心管理团队
             $team_list = $fund_company_obj['team_list'];
            // 核心优势等七大块
            $plans_obj = $response['plans_obj'];
            // 更多详细信息
            $attachment_list = $response['attachment_list'];
            // 基金档案有无
            foreach ($attachment_list as $k => $v) {
                $att_url = $v['att_url'];
                if ($att_url) {
                    $GLOBALS['tmpl']->assign("att_url", 1);
                } else {
                    $GLOBALS['tmpl']->assign("att_url", 0);
                }
            }
            
        } else {
            // 404
            app_redirect(url("error#index"));
        }
        // 股份确认已完成
        $pc_status = $GLOBALS['db']->getOne("select  fund.actual_invest_confirm as  confirm  from cixi_fund_expectant_investor as fund,cixi_user as user,cixi_fund as fund_name  where fund_name.id=fund.fund_id and user.is_effect=1 and user.id=fund.user_id and fund_id=$fund_id  and user_id=$user_id ");
        
        $GLOBALS['tmpl']->assign("response", $response);
        $GLOBALS['tmpl']->assign("profession_info", $profession_info);
        $GLOBALS['tmpl']->assign("invest_philosophy", $invest_philosophy);
        $GLOBALS['tmpl']->assign("intend_deal_list", $intend_deal_list);
        $GLOBALS['tmpl']->assign("fund_company_obj", $fund_company_obj);
        $GLOBALS['tmpl']->assign("team_list", $team_list);
        $GLOBALS['tmpl']->assign("plans_obj", $plans_obj);
        $GLOBALS['tmpl']->assign("attachment_list", $attachment_list);
        $GLOBALS['tmpl']->assign("pc_status", $pc_status);
        $GLOBALS['tmpl']->display("fund_raising_details.html");
    }
}
?>