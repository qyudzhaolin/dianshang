<?php
namespace Library;

require_once(LIB_PATH . 'UmengPush/autoload.php');

class Umeng{
	
	function sendBroadCast($text,$url){
		// andriod 推送
		$uMengAndriod = new  \UmengPush\Umeng(C('UMENG_ANDROID_APP_KEY'),C('UMENG_ANDROID_APP_SECRET'));
		$uMengAndriod->sendAndroidBroadcast($text,$url);
		
		// ios 推送
		$uMengIOS = new  \UmengPush\Umeng(C('UMENG_IOS_APP_KEY'),C('UMENG_IOS_APP_SECRET'));
		$uMengIOS->sendIOSBroadcast($text,$url);
	}
	
	
	
	
	function sendUniCast(){
		$uMeng = new  \UmengPush\Umeng(C('UMENG_ANDROID_APP_KEY'),C('UMENG_ANDROID_APP_SECRET'));
		$uMeng->sendAndroidUnicast();
		
		// ios 推送
		$uMengIOS = new  \UmengPush\Umeng(C('UMENG_IOS_APP_KEY'),C('UMENG_IOS_APP_SECRET'));
		$uMengIOS->sendIOSUnicast();
	}
}