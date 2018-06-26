<?php
namespace UmengPush;

use UmengPush\Android\AndroidBroadcast;
use UmengPush\Android\AndroidUnicast;
use UmengPush\IOS\IOSBroadcast;
use UmengPush\IOS\IOSUnicast;
use System\Logs;

class Umeng {
	protected $appkey           = NULL; 
	protected $appMasterSecret     = NULL;
	protected $timestamp        = NULL;
	protected $validation_token = NULL;

	function __construct($key,$secret) {
		$this->appkey = $key;
		$this->appMasterSecret = $secret;
		$this->timestamp = strval(time());
	}

	// 向andriod设备广播
	function sendAndroidBroadcast($text,$url) {
		try {
			$brocast = new AndroidBroadcast();
			$brocast->setAppMasterSecret($this->appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			$brocast->setPredefinedKeyValue("ticker",           "磁斯达克资讯");
			$brocast->setPredefinedKeyValue("title",            "磁斯达克");
			$brocast->setPredefinedKeyValue("text",             $text);
			$brocast->setPredefinedKeyValue("after_open",       "go_custom");
			$brocast->setPredefinedKeyValue("custom", 			$url);
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			//TODO 从环境变量配置文件取值
			$brocast->setPredefinedKeyValue("production_mode",  C('UMENG_PRODUCTION_MODE'));
			// [optional]Set extra fields
			$brocast->setExtraField("url", $url);
			Logs::info("Library。UmengPush.sendAndriodBroadCast",LOG_FLAG_NORMAL,['开始向andriod设备进行广播','推送中。。。']);
			$brocast->send();
			Logs::info("Library。UmengPush.sendAndriodBroadCast",LOG_FLAG_NORMAL,['开始向andriod设备进行广播','推送完成']);
		} catch (Exception $e) {
			Logs::err("Library。UmengPush.sendAndriodBroadCast",LOG_FLAG_NORMAL,['开始向andriod设备进行广播，推送异常',$e->getMessage()]);
		}
	}

	function sendAndroidUnicast() {
		try {
			$unicast = new AndroidUnicast();
			$unicast->setAppMasterSecret($this->appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$unicast->setPredefinedKeyValue("device_tokens",    "AlzHyGlrH445mSqP6FYVghBhvWiOBW1m0KjDd5rIyUri"); 
			$unicast->setPredefinedKeyValue("ticker",           "Android unicast ticker111");
			$unicast->setPredefinedKeyValue("title",            "Android unicast title11");
			$unicast->setPredefinedKeyValue("text",             "Android unicast text111");
			$unicast->setPredefinedKeyValue("after_open",       "go_custom");
			$unicast->setPredefinedKeyValue("custom","http://api.cisdaq.com/v4/push/25");
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			$unicast->setPredefinedKeyValue("production_mode",  C('UMENG_PRODUCTION_MODE'));
			// Set extra fields
			$unicast->setExtraField("url", "http://api.cisdaq.com/v4/push/25");
			print("Sending unicast notification, please wait...\r\n");
			$unicast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

// 	function sendAndroidFilecast() {
// 		try {
// 			$filecast = new AndroidFilecast();
// 			$filecast->setAppMasterSecret($this->appMasterSecret);
// 			$filecast->setPredefinedKeyValue("appkey",           $this->appkey);
// 			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);
// 			$filecast->setPredefinedKeyValue("ticker",           "Android filecast ticker");
// 			$filecast->setPredefinedKeyValue("title",            "Android filecast title");
// 			$filecast->setPredefinedKeyValue("text",             "Android filecast text");
// 			$filecast->setPredefinedKeyValue("after_open",       "go_app");  //go to app
// 			print("Uploading file contents, please wait...\r\n");
// 			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
// 			$filecast->uploadContents("aa"."\n"."bb");
// 			print("Sending filecast notification, please wait...\r\n");
// 			$filecast->send();
// 			print("Sent SUCCESS\r\n");
// 		} catch (Exception $e) {
// 			print("Caught exception: " . $e->getMessage());
// 		}
// 	}

// 	function sendAndroidGroupcast() {
// 		try {
// 			/* 
// 		 	 *  Construct the filter condition:
// 		 	 *  "where": 
// 		 	 *	{
//     	 	 *		"and": 
//     	 	 *		[
//       	 	 *			{"tag":"test"},
//       	 	 *			{"tag":"Test"}
//     	 	 *		]
// 		 	 *	}
// 		 	 */
// 			$filter = 	array(
// 							"where" => 	array(
// 								    		"and" 	=>  array(
// 								    						array(
// 							     								"tag" => "test"
// 															),
// 								     						array(
// 							     								"tag" => "Test"
// 								     						)
// 								     		 			)
// 								   		)
// 					  	);
					  
// 			$groupcast = new AndroidGroupcast();
// 			$groupcast->setAppMasterSecret($this->appMasterSecret);
// 			$groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
// 			$groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
// 			// Set the filter condition
// 			$groupcast->setPredefinedKeyValue("filter",           $filter);
// 			$groupcast->setPredefinedKeyValue("ticker",           "Android groupcast ticker");
// 			$groupcast->setPredefinedKeyValue("title",            "Android groupcast title");
// 			$groupcast->setPredefinedKeyValue("text",             "Android groupcast text");
// 			$groupcast->setPredefinedKeyValue("after_open",       "go_app");
// 			// Set 'production_mode' to 'false' if it's a test device. 
// 			// For how to register a test device, please see the developer doc.
// 			$groupcast->setPredefinedKeyValue("production_mode", "false");
// 			print("Sending groupcast notification, please wait...\r\n");
// 			$groupcast->send();
// 			print("Sent SUCCESS\r\n");
// 		} catch (Exception $e) {
// 			print("Caught exception: " . $e->getMessage());
// 		}
// 	}

// 	function sendAndroidCustomizedcast() {
// 		try {
// 			$customizedcast = new AndroidCustomizedcast();
// 			$customizedcast->setAppMasterSecret($this->appMasterSecret);
// 			$customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
// 			$customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
// 			// Set your alias here, and use comma to split them if there are multiple alias.
// 			// And if you have many alias, you can also upload a file containing these alias, then 
// 			// use file_id to send customized notification.
// 			$customizedcast->setPredefinedKeyValue("alias",            "xx");
// 			// Set your alias_type here
// 			$customizedcast->setPredefinedKeyValue("alias_type",       "xx");
// 			$customizedcast->setPredefinedKeyValue("ticker",           "Android customizedcast ticker");
// 			$customizedcast->setPredefinedKeyValue("title",            "Android customizedcast title");
// 			$customizedcast->setPredefinedKeyValue("text",             "Android customizedcast text");
// 			$customizedcast->setPredefinedKeyValue("after_open",       "go_app");
// 			print("Sending customizedcast notification, please wait...\r\n");
// 			$customizedcast->send();
// 			print("Sent SUCCESS\r\n");
// 		} catch (Exception $e) {
// 			print("Caught exception: " . $e->getMessage());
// 		}
// 	}

	function sendIOSBroadcast($text,$url) {
		try {
			$brocast = new IOSBroadcast();
			$brocast->setAppMasterSecret($this->appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			$brocast->setPredefinedKeyValue("alert", $text);
			$brocast->setPredefinedKeyValue("badge", 0);
			$brocast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$brocast->setPredefinedKeyValue("production_mode", C('UMENG_PRODUCTION_MODE'));
			// Set customized fields
			$brocast->setCustomizedField("notiPath", $url);
			Logs::info("Library.Umeng.sendIOSBroadcast",LOG_FLAG_NORMAL,["向IOS设备广播通知","消息推送中..."]);
			$brocast->send();
			Logs::info("Library.Umeng.sendIOSBroadcast",LOG_FLAG_NORMAL,["向IOS设备广播通知","消息推送完成"]);
		} catch (Exception $e) {
			Logs::err("Library.Umeng.sendIOSBroadcast",LOG_FLAG_NORMAL,["向IOS设备广播通知","通知异常",$e->getMessage()]);
		}
	}

	function sendIOSUnicast() {
		try {
			$unicast = new IOSUnicast();
			$unicast->setAppMasterSecret($this->appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$unicast->setPredefinedKeyValue("device_tokens",    "93d549d1cac90ebdc4d42e44cfa001fcb1d727fa4750e6b64bb1f55856d856c9"); 
			$unicast->setPredefinedKeyValue("alert", "IOS 单播测试");
			$unicast->setPredefinedKeyValue("badge", 0);
			$unicast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$unicast->setPredefinedKeyValue("production_mode", C('UMENG_PRODUCTION_MODE'));
			// Set customized fields
			$unicast->setCustomizedField("notiPath", "http://api.cisdaq.com/v4/push/26");
			Logs::info("Library.Umeng.sendIOSUniCast",LOG_FLAG_NORMAL,["向IOS设备,device token:"."11111"."单播通知","消息推送中..."]);
			$unicast->send();
			Logs::info("Library.Umeng.sendIOSUniCast",LOG_FLAG_NORMAL,["向IOS设备,device token:"."11111"."单播通知","消息推送完成"]);
		} catch (Exception $e) {
			Logs::info("Library.Umeng.sendIOSUniCast",LOG_FLAG_NORMAL,["向IOS设备单播通知","通知异常",$e->getMessage()]);
		}
	}

// 	function sendIOSFilecast() {
// 		try {
// 			$filecast = new IOSFilecast();
// 			$filecast->setAppMasterSecret($this->appMasterSecret);
// 			$filecast->setPredefinedKeyValue("appkey",           $this->appkey);
// 			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);

// 			$filecast->setPredefinedKeyValue("alert", "IOS 文件播测试");
// 			$filecast->setPredefinedKeyValue("badge", 0);
// 			$filecast->setPredefinedKeyValue("sound", "chime");
// 			// Set 'production_mode' to 'true' if your app is under production mode
// 			$filecast->setPredefinedKeyValue("production_mode", "false");
// 			print("Uploading file contents, please wait...\r\n");
// 			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
// 			$filecast->uploadContents("aa"."\n"."bb");
// 			print("Sending filecast notification, please wait...\r\n");
// 			$filecast->send();
// 			print("Sent SUCCESS\r\n");
// 		} catch (Exception $e) {
// 			print("Caught exception: " . $e->getMessage());
// 		}
// 	}

// 	function sendIOSGroupcast() {
// 		try {
// 			/* 
// 		 	 *  Construct the filter condition:
// 		 	 *  "where": 
// 		 	 *	{
//     	 	 *		"and": 
//     	 	 *		[
//       	 	 *			{"tag":"iostest"}
//     	 	 *		]
// 		 	 *	}
// 		 	 */
// 			$filter = 	array(
// 							"where" => 	array(
// 								    		"and" 	=>  array(
// 								    						array(
// 							     								"tag" => "iostest"
// 															)
// 								     		 			)
// 								   		)
// 					  	);
					  
// 			$groupcast = new IOSGroupcast();
// 			$groupcast->setAppMasterSecret($this->appMasterSecret);
// 			$groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
// 			$groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
// 			// Set the filter condition
// 			$groupcast->setPredefinedKeyValue("filter",           $filter);
// 			$groupcast->setPredefinedKeyValue("alert", "IOS 组播测试");
// 			$groupcast->setPredefinedKeyValue("badge", 0);
// 			$groupcast->setPredefinedKeyValue("sound", "chime");
// 			// Set 'production_mode' to 'true' if your app is under production mode
// 			$groupcast->setPredefinedKeyValue("production_mode", "false");
// 			print("Sending groupcast notification, please wait...\r\n");
// 			$groupcast->send();
// 			print("Sent SUCCESS\r\n");
// 		} catch (Exception $e) {
// 			print("Caught exception: " . $e->getMessage());
// 		}
// 	}

// 	function sendIOSCustomizedcast() {
// 		try {
// 			$customizedcast = new IOSCustomizedcast();
// 			$customizedcast->setAppMasterSecret($this->appMasterSecret);
// 			$customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
// 			$customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

// 			// Set your alias here, and use comma to split them if there are multiple alias.
// 			// And if you have many alias, you can also upload a file containing these alias, then 
// 			// use file_id to send customized notification.
// 			$customizedcast->setPredefinedKeyValue("alias", "xx");
// 			// Set your alias_type here
// 			$customizedcast->setPredefinedKeyValue("alias_type", "xx");
// 			$customizedcast->setPredefinedKeyValue("alert", "IOS 个性化测试");
// 			$customizedcast->setPredefinedKeyValue("badge", 0);
// 			$customizedcast->setPredefinedKeyValue("sound", "chime");
// 			// Set 'production_mode' to 'true' if your app is under production mode
// 			$customizedcast->setPredefinedKeyValue("production_mode", "false");
// 			print("Sending customizedcast notification, please wait...\r\n");
// 			$customizedcast->send();
// 			print("Sent SUCCESS\r\n");
// 		} catch (Exception $e) {
// 			print("Caught exception: " . $e->getMessage());
// 		}
// 	}
}

// Set your appkey and master secret here
//$demo = new UmengPush("your appkey", "your app master secret");
// $demo->sendAndroidUnicast();
/* these methods are all available, just fill in some fields and do the test
 * $demo->sendAndroidBroadcast();
 * $demo->sendAndroidFilecast();
 * $demo->sendAndroidGroupcast();
 * $demo->sendAndroidCustomizedcast();
 *
 * $demo->sendIOSBroadcast();
 * $demo->sendIOSUnicast();
 * $demo->sendIOSFilecast();
 * $demo->sendIOSGroupcast();
 * $demo->sendIOSCustomizedcast();
 */