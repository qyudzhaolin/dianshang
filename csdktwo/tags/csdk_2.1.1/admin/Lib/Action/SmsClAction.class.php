<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class SmsClAction extends CommonAction{
    
	public function index(){
	    
	    // 查询所有用户
	    $map['user_type'] = 1; # 投资者
	    $map['is_review'] = 1; # 已审核
	    $map['is_effect'] = 1; # 已启用
        $User = M('User');
	    $list = $User->field('id,mobile,user_name')->where($map)->select();

	    $this->assign('list',$list);
	    $this->display();
	}
	
	public function sendSMS(){
	           
	    $msg = trim($_REQUEST['msg']);
	    $mobiles = trim($_REQUEST['mobiles']);
	    
	    if(empty($msg) || empty($mobiles)){
	        $this->error("请先完善信息");
	    }
	    
	    // 发送短信
	    $params = array(
	        "mobile"    => $mobiles,
	        "content"   => $msg,
	        "type"      => getSendSmsType("admin_sms"),
	    );
	    $result = request_service_api("Common.Sms.sendSms",$params);
	    
	    if($result['status '] == 0){
	        $this->success("发送成功");
	    }else{
	        $this->error("发送失败：".$result['message']);
	    }
	    
	}
}
?>