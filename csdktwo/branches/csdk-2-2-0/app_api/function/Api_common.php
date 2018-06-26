<?php 
/**
 * 请求一个服务层Api----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: CISDAQ TEAM
 * +----------------------------------------------------------------------
 * | Version: All
 * +----------------------------------------------------------------------
 * |
 */
function request_service_api($func, $params=array() ){
	require 'Curl.class.php';
	$curl = new Curl();
	$curl->setOpt(CURLOPT_TIMEOUT, 15);
	$curl->setOpt(CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded; charset=UTF-8'));
	$curl->setOpt(CURLOPT_FRESH_CONNECT, true);
	$curl->setOpt(CURLOPT_ENCODING , 'gzip');
	$curl->setOpt(CURLOPT_USERAGENT, 'CSDK_WAP_UI_LAYER');
	
	$func = substr_count($func, '.')===1 ?  SERVICE_API_DEFAULT_MODULE.'.'.$func : $func;
	$params = array(
			'method' => 'Wap',
			'func' =>  $func,
			'params' =>  json_encode($params),
	);
	$params['signKey'] = md5($params['params'].SERVICE_API_REQUEST_SECKEY.$params['func']);
	
	$requestEntry = SERVICE_API_HOST.SERVICE_API_HOST_ENTRY;
	
	$curl->post($requestEntry,$params);
	
	if ($curl->error){
		return null;
	}
	
	return  json_decode($curl->response, true);
}
?>