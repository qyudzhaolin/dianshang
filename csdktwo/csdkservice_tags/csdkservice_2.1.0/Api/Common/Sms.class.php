<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
namespace Api\Common;
use System\Base;
class Sms extends Base{
	private $SMS = '';                             #SMS服务
	public static $TYPE_CODE = 0;                  #验证码
	public static $TYPE_ADMIN_SMS = 1;             #后台短信群发
	public static $TYPE_RESET_PASSWORD = 2;        #重置密码
	public static $TYPE_USER_CERT = 3;             #会员认证通过
	public static $TYPE_ADMIN_ADD_USER = 4;        #后台开通账号
	public static $TYPE_FUND_BIND_INVESTOR = 5;    #基金绑定投资人
	public static $TYPE_FINANCE_FINISH = 6;        #融资完成
	public static $needstatus='false';             #是否需要状态报告
	public static $product='';                     #产品id
	public static $extno= '';                      #扩展码
	private $tablePrefix = null;                   #表前缀
	private $table = null;                         #数据表
	
	public function __construct(){
		parent::__construct();
		$this->tablePrefix = $this->getMainDbTablePrefix();
		$this->table = "{$this->tablePrefix}deal_msg_list";
		if(empty($this->SMS)){
			$this->SMS = new \Library\Sms\ChuanglanSmsApi();
		}
    }
	/*  发短信记录日志
     * 如果不传log日志只发短信，传的话发短信记录日志
     * @param string $mobile 手机号码，多个用英文,隔开
     * @param string $content 短信内容
     * @param string $needstatus 是否需要状态报告(侧启动一个HTTP服务用于接收状态报告)
     * @param string $product 产品id，可选
     * @param string $extno   扩展码，可选
     * @return array('resptime','respstatus','msgid',...,'code'=>'状态码','msg'=>'状态码中文含义');
    */
	public function sendSmsOnly($params){
	 if(!empty($params)){
    	$mobiles = $params['mobiles'];
    	$content=$params['content'];
    	$log=$params['log'];
     }
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
        
        if( $result['msg']=="提交成功"){
        	if(!empty($log)){
        $re = $this->logToTable($mobiles, $content, $log);
        $result['msg2'] = $re ? '添加日志成功' : '添加日志失败';
        	}
        }
     return  $this->endResponse($result);
	  
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
	    $is_success=0;
	    $type = isset($log['type']) ? $log['type'] : self::$TYPE_CODE;  # 默认0
	    $code = $type == 0 ? $content: 0;                              # type=0时 code为验证码
	    foreach($mobiles as $v){
	    	$sql= "INSERT INTO {$this->table}(`code`,`mobile_num`,`send_type`,`content`,`send_time`,`is_success`) VALUES(:code,:mobile_num,:send_type,:content,:send_time,:is_success)";
	    	$args = ['code'=>$code,'mobile_num'=>$v,'send_type'=>$type,'content'=>$content,'send_time'=>$time,'is_success'=>$is_success];
	    	$data = $this->executeNonQuery($sql,$args);
	    
	    }
	    return true;
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
}
?>