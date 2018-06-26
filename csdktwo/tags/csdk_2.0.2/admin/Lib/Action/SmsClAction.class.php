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
	    require_once APP_ROOT_PATH.'system/utils/csdk_sms.php';
	    $SMS = new csdk_sms();
	    $result = $SMS->sendSMS($mobiles,$msg,array('type'=>csdk_sms::$TYPE_ADMIN_SMS));
	    
	    if($result['code'] == 0){
	        $this->success("发送成功");
	    }else{
	        $this->error("发送失败：".$result['msg']);
	    }
	    
	}
}
?>