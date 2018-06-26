<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class csdk_sms{
    
	private $SMS = '';                             #SMS服务
	
	public static $FROM_ADMIN = 'admin';           #后台admin
	public static $FROM_APP = 'app';               #前台app
	public static $FROM_API = 'api';               #api
	
	public static $TYPE_CODE = 0;                  #验证码
	public static $TYPE_ADMIN_SMS = 1;             #后台短信群发
	public static $TYPE_RESET_PASSWORD = 2;        #重置密码
	public static $TYPE_USER_CERT = 3;             #会员认证通过
	public static $TYPE_ADMIN_ADD_USER = 4;        #后台开通账号
	public static $TYPE_FUND_BIND_INVESTOR = 5;    #基金绑定投资人
	public static $TYPE_FINANCE_FINISH = 6;        #融资完成
	
	public function __construct(){
		if(empty($this->SMS)){
			require_once APP_ROOT_PATH."system/sms/CL_sms.php";
			$this->SMS = new ChuanglanSmsApi();
		}
    }
    
    /*
     * 发送短信并记录日志
     * @param string $mobile 手机号码，多个用英文,隔开
     * @param string $content 短信内容
     * @param array  $log 记录日志参数  from : 来源 type: 类型
     * @param string $needstatus 是否需要状态报告(侧启动一个HTTP服务用于接收状态报告)
     * @param string $product 产品id，可选
     * @param string $extno   扩展码，可选
     * @return array('resptime','respstatus','msgid',...,'code'=>'状态码','msg'=>'状态码中文含义');
    */
    public function sendSms($mobiles, $content, $log, $needstatus = 'false', $product = '', $extno = ''){
         
        $result = $this->sendSmsOnly($mobiles, $content, $needstatus, $product, $extno);
        
        // send success
        if($result['code'] == 0){
            // 记录日志，目前只往表 cixi_deal_msg_list 中添加纪录
            $re = $this->logToTable($mobiles, $content, $log);
            $result['msg2'] = $re ? '添加日志成功' : '添加日志失败';
        }
        return $result;
    }
    
	/*
     * 单纯的只发送短信
     * @param string $mobile 手机号码，多个用英文,隔开
     * @param string $content 短信内容
     * @param string $needstatus 是否需要状态报告(侧启动一个HTTP服务用于接收状态报告)
     * @param string $product 产品id，可选
     * @param string $extno   扩展码，可选
     * @return array('resptime','respstatus','msgid',...,'code'=>'状态码','msg'=>'状态码中文含义');
    */
	public function sendSmsOnly($mobiles, $content, $needstatus = 'false', $product = '', $extno = ''){

        $mobiles_ = explode(',',$mobiles);
        if(count($mobiles_) > 0){
            if(empty($this->SMS)){
                $result['status']  = 107;
                $result['msg']     = "没有实例化SMS服务";
            }else{
                $result = $this->SMS->sendSMS($mobiles, $content, $needstatus, $product, $extno);
                $result = $this->SMS->execResult($result);
                $result['code']     = $result[1];
                $result['msg']      = $this->SMS->getSmsSubmit($result[1]);
            }
        }else{
            $result['code']  = 107;
            $result['msg']   = "没有发送的手机号";
        }
	    
	    return $result;
	}
	
	/*
	 * 记录日志 (cixi_deal_msg_list)
	 * @param array  $mobiles 手机号码
	 * @param string $content 短信内容
	 * @param array  $log 记录日志参数  from : 来源 type: 类型
	 */
	private function logToTable($mobiles, $content, $log){
	    
        $mobiles = explode(',',$mobiles);
	    
	    // 涉及字段：code, mobile_num, send_type, content, send_time, is_success
	    $time = time();
	    $from = isset($log['from']) ? $log['from'] : self::$FROM_ADMIN; # 默认admin
	    $type = isset($log['type']) ? $log['type'] : self::$TYPE_CODE;  # 默认0
	    $code = $type == 0 ? $content : 0;                              # type=0时 code为验证码
	    
// 	    $sql = "INSERT DELAYED INTO __TABLE__(`mobile_num`,`send_type`,`content`,`send_time`,`is_success`) VALUES{$values}";
	    
	    $res = false;
	    
	    if($from == self::$FROM_API){
	        
	        foreach($mobiles as $v){
    	        $sql       = "INSERT INTO cixi_deal_msg_list(`code`,`mobile_num`,`send_type`,`content`,`send_time`,`is_success`) VALUES(?,?,?,?,?,?)";
    	        $para      = array($code,$v,$type,$content,$time,0);
    	        $result    = PdbcTemplate::execute($sql, $para);
	        }
	        
	    }else{
	        
	        // 组装value
	        $values = '';
	        foreach($mobiles as $v){
	            $values .= "('{$code}','{$v}','{$type}','{$content}','{$time}',0),";
	        }
	        $values = rtrim($values,',');
	        
	        // admin
	        if($from == self::$FROM_ADMIN){
	            $sql = "INSERT INTO __TABLE__(`code`,`mobile_num`,`send_type`,`content`,`send_time`,`is_success`) VALUES{$values}";
	            $Msg = M('dealMsgList');
	            $res = $Msg->execute($sql);
	        }
	        
	        // app
	        if($from == self::$FROM_APP){
	            $sql = "INSERT INTO ".DB_PREFIX."deal_msg_list(`code`,`mobile_num`,`send_type`,`content`,`send_time`,`is_success`) VALUES{$values}";
	            $GLOBALS['db']->query($sql);
	        }
	        
	    }
	    
	    return $res;
	}
	
	public function getSendType(){
	    return array(
	        self::$TYPE_CODE => '验证码',
	        self::$TYPE_ADMIN_SMS => '后台短信群发',
	        self::$TYPE_RESET_PASSWORD => '重置密码',
	        self::$TYPE_USER_CERT => '会员认证通过',
	        self::$TYPE_ADMIN_ADD_USER => '后台开通账号',
	        self::$TYPE_FUND_BIND_INVESTOR => '基金绑定投资人',
	        self::$TYPE_FINANCE_FINISH => '融资完成',
	    );
	}
	
	public function getSendFrom(){
	    return array(
	        self::$FROM_ADMIN => '后台',
	        self::$FROM_APP => '前台',
	        self::$FROM_API => 'api',
	    );
	}
	
}
?>