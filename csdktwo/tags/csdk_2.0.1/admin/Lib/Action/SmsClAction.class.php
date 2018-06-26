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
	    require_once APP_ROOT_PATH.'system/utils/ChuanglanSmsApi.php';
	    $result = sendSMS($mobiles,$msg);
	    
	    if($result[1] == 0){
	        
    	    // 短信记录
	        $mobiles = explode(',',$mobiles);
	        $values = '';
	        // mobile_num, send_type, content, send_time, is_success
	        $time = time();
	        foreach($mobiles as $v){
	            $values .= "('{$v}',1,'{$msg}','{$time}',0),";
	        }
	        $values = rtrim($values,',');
// 	        $sql = "INSERT DELAYED INTO __TABLE__(`mobile_num`,`send_type`,`content`,`send_time`,`is_success`) VALUES{$values}";
	        $sql = "INSERT INTO __TABLE__(`mobile_num`,`send_type`,`content`,`send_time`,`is_success`) VALUES{$values}";
	        $Msg = M('dealMsgList');
	        $Msg->execute($sql);
	        
	        $this->success("发送成功");
	    }else{
	        $this->error("发送失败：".$result['info']);
	    }
	    
	}
}
?>