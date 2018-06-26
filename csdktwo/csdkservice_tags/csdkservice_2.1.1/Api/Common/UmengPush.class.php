<?php
namespace Api\Common;

use System\Base;
use System\Logs;

class UmengPush extends Base{
	
	public function __construct() {
        parent::__construct();
    }
	
	public function sendUniCast($param){
		$umeng = new \Library\Umeng();
		$umeng->sendUniCast();
		return $this->endResponse("success",0);
	}
	
	// 参数为数组结构，且至少包含两个参数
	public function sendBroadcast($param){
		Logs::info('Common.UmengPush.sendBroadcast',LOG_FLAG_NORMAL,['向设备推送通知的参数',$param]);
		if (empty($param) || count($param) <=0 || empty($param['text']) ||empty($param['url'])){
			return $this->endResponse(NULL,1001);
		}
		$umeng = new \Library\Umeng();
		$umeng->sendBroadCast($param['text'], $param['url']);
		Logs::info('Common.UmengPush.sendBroadcast',LOG_FLAG_NORMAL,['向设备推送通知完成，推送参数为：',$param]);
		return $this->endResponse("push success",0);
	}
	
	public function test($params){
		Logs::info('Common.UmengPush.test',LOG_FLAG_NORMAL,['向设备推送通知的参数',$params]);
		
		return $this->endResponse('success',0);
	}
}