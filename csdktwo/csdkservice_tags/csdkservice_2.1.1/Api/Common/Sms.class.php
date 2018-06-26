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
use System\Logs;

class Sms extends Base{
    
	private $SMS = '';                             #SMS服务
	
	// 此类变量的引用暂时不通过服务层，统一放到项目中了
	public static $SOURCE_ADMIN = 'admin';         #后台admin
	public static $SOURCE_WEB = 'web';             #网站web
	public static $SOURCE_APP = 'app';             #app
	
	public static $TYPE_CODE = 0;                  #验证码
	public static $TYPE_ADMIN_SMS = 1;             #后台短信群发
	public static $TYPE_USER_CERT = 3;             #会员认证通过
	public static $TYPE_USER_AUDIT = 4;            #会员审核
	public static $TYPE_FUND_BIND_INVESTOR = 5;    #基金绑定投资人
	public static $TYPE_FINANCE_FINISH = 6;        #融资完成
	
	public function __construct(){
		parent::__construct();
		
		if(empty($this->SMS)){
			$this->SMS = new \Library\Sms\ChuanglanSmsApi();
		}
    }
    
    /*
     * 白名单
     */
    private function getWhitelistMobile(){
    
        return C( 'WHITE_LIST_TEL' );
    
    }
    
    /*
     * 单纯的只发送短信
     * @param string $params['mobile']      手机号码，多个用英文 , 隔开
     * @param string $params['content']     短信内容
     * @param string $params['needstatus']  是否需要状态报告(侧启动一个HTTP服务用于接收状态报告)
     * @param string $params['product']     产品id，可选
     * @param string $params['extno']       扩展码，可选
     * @param boolean $flag                 返回方式标识位，默认false直接返回接口数据（api条用无需该参数，类内部使用）
     * @return array('resptime','respstatus','msgid',...,'status'=>'状态码','message'=>'状态码中文含义');
     */
    public function sendSmsOnly($params, $flag = false){
        
        Logs::info('Common.Sms.sendSmsOnly',LOG_FLAG_NORMAL,['sendSmsOnly-发送短信，传入的参数',$params]);

        $mobiles = array_filter(explode(',',$params['mobile']));
        $content = trim($params['content']);
        
        if(count($mobiles) > 0 && !empty($content)){
            
            if(empty($this->SMS)){
                return $this->endResponse(null,3000);
            }else{
                
                // 过滤白名单
                $whiteList  = $this->getWhitelistMobile();
                if(!empty($whiteList)){
                    $mobiles    = array_intersect($mobiles, $whiteList);    #取交集
                }
                
                if(count($mobiles) > 0){
                    
                    // 接受参数
                    $mobiles    = implode(',',$mobiles);
                    $needstatus = isset($params['needstatus']) ? $params['needstatus'] : false;
                    $product    = isset($params['product']) ? $params['product'] : "";
                    $extno      = isset($params['extno']) ? $params['extno'] : "";
                    
                    // 发送短信
                    $result = $this->SMS->sendSMS($mobiles, $content, $needstatus, $product, $extno);
                    $result = $this->SMS->execResult($result);
                    return $flag ? $result : $this->endResponse($result,$result[1],null,$this->SMS->getSmsSubmit($result[1]));
                
                }else{
                    return $this->endResponse(null,3001);
                }
            }
            
        }
        
        return $this->endResponse(null,5);
    }
    

    /*
     * 发送短信并记录发送的内容（入表）
     * @param string $params['mobile']      手机号码，多个用英文 , 隔开
     * @param string $params['content']     短信内容
     * @param string $params['source']      记录日志参数：来源，默认admin
     * @param string $params['type']        记录日志参数：类型，默认0 验证码
     * @param string $params['code']        验证码，type=0时传入
     * @param string $params['needstatus']  是否需要状态报告(侧启动一个HTTP服务用于接收状态报告)
     * @param string $params['product']     产品id，可选
     * @param string $params['extno']       扩展码，可选
     * @return array('resptime','respstatus','msgid',...,'status'=>'状态码','message'=>'状态码中文含义');
     */
    public function sendSms($params){
    
        //         Logs::info('Common.Sms.sendSms',LOG_FLAG_NORMAL,['sendSms-发送短信，传入的参数',$params]);
    
        // 参数验证，记录日志的需要传入参数source type
        //         if(!isset($params['source']) || !isset($params['type'])){
        //             return $this->endResponse(null,5);
        //         }
    
        $result = $this->sendSmsOnly($params, true);
    
        // send success
        if($result[1] == 0){
            // 记录日志，目前只往表 cixi_deal_msg_list 中添加纪录
            $this->logToTable($params);
        }
    
        return $this->endResponse($result,$result[1],null,$this->SMS->getSmsSubmit($result[1]));
    }
    
    /*
     * 暂时不用
     */
    public function sendSmsConditional($params){
        return false;
    }
    
    /*
     * 记录发送的内容（入表） (cixi_deal_msg_list)
     * @param string $params['mobile']      手机号码，多个用英文 , 隔开
     * @param string $params['content']     短信内容
     * @param string $params['code']        验证码，type=0时传入
     * @param string $params['source']      记录日志参数：来源
     * @param string $params['type']        记录日志参数：类型
     */
    private function logToTable($params){
         
        // 涉及字段：code, mobile_num, dest, send_type, content, send_time, is_success
        $time = time();
        $from = isset($params['source']) ? trim($params['source']) : self::$SOURCE_ADMIN;  # 默认admin
        $type = isset($params['type']) ? (int)$params['type'] : self::$TYPE_CODE;          # 默认0 验证码
        $code = isset($params['code']) ? (int)$params['code'] : 0;                         # type=0时 code为验证码
        
        $table      = $this->getMainDbTablePrefix()."deal_msg_list";
        $mobiles    = array_filter(explode(',',$params['mobile']));
        foreach($mobiles as $v){
            $sql    = "INSERT INTO {$table}(`code`,`mobile_num`,`dest`,`send_type`,`content`,`send_time`,`is_success`) VALUES(:code,:mobile_num,:dest,:send_type,:content,:send_time,:is_success)";
            $args   = ['code'=>$code,'mobile_num'=>$v,'dest'=>$from,'send_type'=>$type,'content'=>$params['content'],'send_time'=>$time,'is_success'=>0];
            $this->executeNonQuery($sql,$args);
        }
        
        return true;
    }
	
}
?>