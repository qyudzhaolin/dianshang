<?php
/**
 * 募集基金详情----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Version: 2.2
 * +----------------------------------------------------------------------
 * | CreateTime: 2016年9月18日12:43:00
 * +----------------------------------------------------------------------
 * |
 */
require_once ('base.php');
require_once ('../Cache/Lite.php');
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$param = array (
		'uid' => isset ( $_POST ['uid'] ) ? trim ( $_POST ['uid'] ) : NULL,
		'fund_id' => isset ( $_POST ['fund_id'] ) ? trim ( $_POST ['fund_id'] ) : 0  
);
$fund_id = isset ( $_POST ['fund_id'] ) ? trim ( $_POST ['fund_id'] ) : NULL;
$version = isset ( $_POST ['version'] ) ? trim ( $_POST ['version'] ) : NULL;
// 查看服务器有没有缓存
$version_db = check_fund_version ( $fund_id );
$options = array (
		'cacheDir' => '../Cache/fund/',
		'lifeTime' => 3600 * 24 * 30 * 365
);
$cache = new Cache_Lite ( $options );

//请求服务层返回身份标示
$request_type=request_service_api('Contents.FundInfo.RaisingUserType',$param);
$response_type=$request_type['response'] ;
$plannum=$request_type['status'] ;
$fund_info_cache = $cache->get ( $fund_id  );
 //var_dump($fund_info_cache);
if ($version>0&&! is_null ( $version )) {
	if ($fund_info_cache) {
		if ($version_db == $version) {
			// 没有新版本，直接返回300
			$obj->status = 300;
			CommonUtil::return_info ( $obj );
			return;
		} else { 
			// 解析cache里的version
			$fund_info_cache = json_decode ( $fund_info_cache ); 
			$version_cache = $fund_info_cache->version;  
			if ($version_db == $version_cache) {  
				$obj->version=$version_db;
				$obj->user_type=    $response_type;
				$obj->data=    $fund_info_cache;
				$obj->status = 200;
				CommonUtil::return_info ( $obj ); 
				return;
			}
		}
	}
}
 

//请求详情数据
$result=request_service_api('Contents.FundInfo.RaisingFundInfo',$param);
$response=$result['response'] ;
 
if ($result['status']==0&&!empty($response)) {
	$obj->version=$version_db;
	$obj->user_type=    $response_type; 
	$obj->data=    $response;
	$obj->status = 200;
}
CommonUtil::return_info ( $obj );
if (! is_null ( $version )) {
	// 写入缓存文件
	$response = json_encode ( $response  );
	$r = $cache->save ( $response , $fund_id );
}
?>