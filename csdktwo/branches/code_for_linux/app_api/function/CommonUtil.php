<?php
/**
 * 用户登录验证----API
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
class CommonUtil{
	public static function verify_user($param = null) {
		require_once('Session.php');
		$obj_r = new stdClass;
		$obj_r->status = 500 ;
		//$p_mobile = trim($_POST["mobile"]) ;
		$uid		 = isset($_POST["uid"])?trim($_POST["uid"]):NULL ;
		$sign_sn   = isset($_POST["sign_sn"])?trim($_POST["sign_sn"]):NULL ;
		$mobile		    = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
		//$p_role_code = trim($_POST["role_code"]) ;
		if (!is_null($param)) {
			$param->mobile 	= $p_mobile ;
			$param->uid			= $p_uid ;
			$param->sign_sn 	= $p_sign_sn ;
			$param->role_code 	= $p_role_code ;	
		}

		if (empty($uid)) {
				$obj_r->r = "用户ID为空" ;
				return $obj_r ;
	    }


		if (empty($sign_sn)) {
				$obj_r->r = "sign_sn为空" ;
				return $obj_r ;
		}
		$token_status=Session::valid_token($uid,$mobile,$sign_sn);
		//var_dump($token_status);
		if($token_status==TOKEN_505)//UID 不存在
		{
			//登录过期
			$obj_r->status = 505;
			$obj_r->r = "登录过期";
			return $obj_r ;
		}
	
		if($token_status==TOKEN_508)//UID 存在， 但是SESSION 不同
		{
			//踢下线
			$obj_r->status = 508;
			$obj_r->r = "被踢下线";
			return $obj_r ;
		}

		$obj_r->status = 200 ;
		return $obj_r;
	}
	
	public static function check_status($user_status)
	{
		$obj_r = new stdClass;
		$obj_r->status = 500 ;
		if(500 == $user_status->status || 505 ==$user_status->status || 508 ==$user_status->status)
		{
			CommonUtil::return_info($user_status);
			exit(-1) ;
		}
		if(200 ==$user_status->status)
		{
			$mobile 	= isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
			if(!is_null($mobile))
			{
				if(preg_match("/^1[34578]\d{9}$/", $mobile))
				{
					//手机号验证通过
					$obj_r->status = 200 ;
					return $obj_r;
				}else{
					//未通过
					$obj_r->status = 500 ;
					return $obj_r;
				}
			}
			$password 	= isset($_POST['password'])? trim($_POST['password']):NULL;
			if(!is_null($password))
			{
				if(preg_match("/^[A-Za-z0-9]{6,12}$/", $password))
				{
					$obj_r->status = 200 ;
					return $obj_r;
				}else{
					$obj_r->status = 500 ;
					return $obj_r;
				}
			}

		}
		/*{
			//verify email
			$email 		= isset($_POST['email'])? trim($_POST['email']):NULL;
			if(!is_null($email))
			{
				if(preg_match("/[a-zA-Z0-9_-.+]+@[a-zA-Z]+/",$email)=0)
				{
						//邮箱验证通过
				}
				else
				{
						//验证不通过： 状态码返回数字几？ 需要规定
				}
			}
			$mobile 		= isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
			if(!is_null($mobile))
			{
				if(preg_match("[0-9]", subject))
				{
						//手机号验证通过
				}
			}

		}*/
	}
	
	public static function return_info($obj)
	{
		echo json_encode($obj,JSON_UNESCAPED_UNICODE);
	}

	public static function Qiniu_Encode($str) // URLSafeBase64Encode
	 {
	    $find = array('+', '/');
	    $replace = array('-', '_');
	    return str_replace($find, $replace, base64_encode($str));
	 }

	public static function getQiniuPath($key) {//$info里面的url
    	$url=BP_DOMAIN.$key;
    	$accessKey = '5CQmagmT27DNXYhsmVewntOpd9VLGD8sC5c02ptg';
		$secretKey = 'lQm-akaIb3JOS-WAlBycTXF_95hx7JEd9s88ARk5';
	    $duetime = time() + 36000;//下载凭证有效时间
	    $DownloadUrl = $url . '?e=' . $duetime;
	    $Sign = hash_hmac ( 'sha1', $DownloadUrl, $secretKey, true );
	    $EncodedSign = CommonUtil::Qiniu_Encode ( $Sign );
	    $Token = $accessKey. ':' . $EncodedSign;
	    $url = $DownloadUrl . '&token=' . $Token;
	
    return $url;
 }
 
 public static function getQiniuIMG($key) {//$info里面的url
 		$url=IMG_DOMAIN.$key;
 	return $url;
 }

}
?>
