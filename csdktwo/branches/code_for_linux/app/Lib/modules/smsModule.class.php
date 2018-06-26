<?php
// +----------------------------------------------------------------------
// | 短信发送验证码
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class smsModule extends BaseModule{
	
	/*
	 * 默认方法
	 */
	public function index(){
		
	    $mobile        = isset($_POST['mobile']) ? trim($_POST['mobile']) : "";
	    $business_type = isset($_POST['business_type']) ? intval($_POST['business_type']) : "";
	    $obj           = array('status' => 999, 'r' => "");
	    
	    if (empty($mobile)) {
	        $obj['r'] = "请输入手机号码";
	        ajax_return($obj);
	    }
	    if (empty($business_type)) {
	        $obj['r'] = "business_type 错误";
	        ajax_return($obj);
	    }
		
	    // 1 注册、换绑手机号码类似
	    $sql       = "select id,is_effect from ".DB_PREFIX."user where mobile='".$mobile."'";
	    $userinfo  = $GLOBALS['db']->getRow($sql);
	    if (1 == $business_type){
    	    if($userinfo['is_effect'] == 1){
    	        $obj['r'] = "该手机号码已注册";
    	        ajax_return($obj);
    	    }else if($userinfo['is_effect'] == 0){
    	        $obj['r'] = "该手机号码已经禁用";
    	        ajax_return($obj);
    	    }
	    }
	    
	    // 2 找回密码类似
	    if (2 == $business_type){
    	    if(empty($userinfo)){
    	        $obj['r'] = "手机号没有注册过";
    	        ajax_return($obj);
    	    }
	    }
		
	    // 查看上次发送时间
	    $time = time();
	    $type = getSendSmsType("code");
	    
	    $sql_select    = "select send_time from ".DB_PREFIX."deal_msg_list where mobile_num = {$mobile} and send_type = {$type} order by send_time asc"; // 最早的时间在上
	    $result_select = $GLOBALS['db']->getAll($sql_select);
	    
	    if (! empty($result_select)) {
	        $msg_count         = count($result_select);
	        $time_difference   = $time - $result_select[0]['send_time']; // 最早的时间
	        $time_difference_late = $time - $result_select[$msg_count - 1]['send_time']; // 最晚的时间
	    
	        if (1 == $msg_count) {
	            if ($time_difference_late < 30) {
	                // 30秒内只能一条
	                $obj['r'] = "同一号码同一签名内容30秒内只能发送一条";
	                ajax_return($obj);
	            }
	        }
	        if (2 == $msg_count) {
	            if ($time_difference_late < 30) {
	                // 30秒内只能一条
	                $obj['r'] = "同一号码同一签名内容30秒内只能发送一条";
	                ajax_return($obj);
	            } elseif ($time_difference < 60) {
	                // 30秒内只能一条
	                $obj['r'] = "同一号码同一签名内容60秒内只能发送2条";
	                ajax_return($obj);
	            }
	        }
	        if (3 <= $msg_count) {
	            if ($time_difference_late < 30) {
	                // 30秒内只能一条
	                $obj['r'] = "同一号码同一签名内容30秒内只能发送一条";
	                ajax_return($obj);
	            } elseif ($time_difference < 60) {
	                // 30秒内只能一条
	                $obj['r'] = "同一号码同一签名内容60秒内只能发送2条";
	                ajax_return($obj);
	            } elseif ($time_difference < 1800) {
	                // 30秒内只能一条
	                $obj['r'] = "同一号码同一签名内容30分钟内不能超过3条";
	                ajax_return($obj);
	            } else {
                    $sql_del       = "delete from ".DB_PREFIX."deal_msg_list where mobile_num = {$mobile} and send_type = {$type}";
                    $result_del    = $GLOBALS['db']->query($sql_del);
	            }
	        }
	    }
	    
	    // 1.生成随机数
	    $code = mt_rand(100000, 999999);
	    $data = getSendSmsTemplate("code", array($code));
	    
	    // 2.发送短信
	    $params = array(
	        "mobile"    => $mobile,
	        "content"   => $data,
	        "source"    => "web",   #值勿改
	        "type"      => $type,
	        "code"      => $code,
	    );
	    $result = request_service_api("Common.Sms.sendSms",$params);
	    
	    if($result['status'] == 0){
	        $obj['status'] = 0;
	        $obj['r'] = "获取验证码成功";
	    }else{
            $obj['status'] = $result['status'];
	        $obj['r'] = "获取验证码失败";
	    }
	    
	    ajax_return($obj);
	}
	
}
?>