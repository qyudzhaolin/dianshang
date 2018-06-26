<?php
/**
 * 配置文件----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Version: 2.1
 * +----------------------------------------------------------------------
 * |
 */
require_once ('../function/Public.php');
// $publickey 为公钥，服务端和客户端统一本地保存，值待定为45F71CC5E94B9E27
// 规则：MD5(cisdaq_api )，16位大写字符串。
define ( 'PUBLICKEY', "45F71CC5E94B9E27" );
function verification_signature($params) {
	if ($params && ! empty ( $params ['signature'] )) {
		$signature = $params ['signature'];
	}

	if (! empty ( $signature )) {
		unset ( $params ['signature'] ); // 销毁signature这个参数，加密匹配其他参数MD5值
		ksort ( $params ); // ksort函数对关联数组按照键名进行升序排序,PS:A-Z
		$params_array = "";
		// 用英文符号"+"链接各个参数,去掉最后一个“+”
		if (! empty ( $params )) {
			foreach ( $params as $key => $val ) {
				$params_array = $params_array . $key . '=' . $val . '+';
			}
			$params_array = (substr ( $params_array, 0, strlen ( $params_array ) - 1 ));
		}
		if (! empty ( $params_array )) {
			$params_key = $params_array . "+";
		} else {
			$params_key = '';
		}

		$md5_params = strtoupper ( substr ( MD5 ( $params_key . PUBLICKEY ), 8, 16 ) );

		if ($md5_params != $signature) {
			return array (
					'false' => false,
					'r' => "签名验证失败"
			);
		}
	} else {
		return array (
				'false' => false,
				'r' => "签名为空"
		);
	}
	return array (
			'false' => true,
			'r' => "签名验证成功"
	);
}
$obj = new stdClass ();
if (verification_signature ( $_POST )['false'] == false) {
	$obj->status = 500;
	$obj->r = verification_signature ( $_POST )['r'];
	CommonUtil::return_info ( $obj );
	die ();
}
function is_exist_mobile($mobile) {
	$sql = "select is_effect from cixi_user where mobile='" . $mobile . "'";
	$user = PdbcTemplate::query ( $sql, null, PDO::FETCH_OBJ, 1 );
	if (! empty ( $user )) {
		$is_effect = $user->is_effect;
		return $is_effect;
	} else 

	{
		return '2';
	}
}
function is_exist_mobile_user($mobile) {
	$sql = "select 1 from cixi_user where mobile='" . $mobile . "'";
	$user = PdbcTemplate::query ( $sql, null, PDO::FETCH_OBJ, 1 );
	if (! empty ( $user )) {
		return true;
	}
	return false;
}
// 验证手机号码
function check_mobile($mobile) {
	if (! empty ( $mobile ) && ! preg_match ( "/^1[34578]\d{9}$/", $mobile )) {
		return false;
	} else {
		return true;
	}
}
// 验证密码
function check_pwd($pwd) {
	if (! preg_match ( "/^[A-Za-z0-9]{6,12}$/", $pwd )) {
		return false;
	} else {
		return true;
	}
}
// 获取项目版本号
function check_version($deal_id) {
	$sql = "select version from cixi_deal where id=" . $deal_id;
	$result = PdbcTemplate::query ( $sql, null, PDO::FETCH_OBJ, 1 );
	if (! empty ( $result )) {
		$version = $result->version;
		return $version;
	} else {
		return false;
	}
}
?>
