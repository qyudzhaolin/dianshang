<?php
/**
 * 公共参数集合----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */
define('RUN_ENV',isset($_SERVER['RUN_ENV'])?$_SERVER['RUN_ENV']:'develop');
require_once('./conf/'.RUN_ENV.'/conf_db.php');
require_once("./fun/CommonUtil.php");
require_once("./fun/PdbcTemplate.php");
require_once("./fun/waf.php");
require_once("./fun/signature_params.php");
require_once("./fun/api_common.php"); 
//require_once(APP_PATH."/api_v1/fun/RedisTemplate.php");

//$publickey 为公钥，服务端和客户端统一本地保存，值待定为45F71CC5E94B9E27
//规则：MD5(cisdaq_api )，16位大写字符串。
define('PUBLICKEY',"45F71CC5E94B9E27");
function verification_signature($params)
{
	
	//var_dump($params);die;
	 $params_array= "";
	 if($params){
	 $signature=$params['signature'];}
	 if(!empty($signature) ){
	 unset($params['signature']);
	 ksort($params);
	 if(!empty($params))
	   	{
	   		foreach ($params as $key => $val)
	   		{
	   			$params_array= $params_array.$key.'='.$val.'+';
	   		}
	   		$params_array=(substr($params_array,0,strlen($params_array)-1));
	   	}
	 if(!empty($params_array)){$params_key=$params_array."+";}else{$params_key='';} 
	 
 	 $md5_params=strtoupper(substr(MD5($params_key.PUBLICKEY),8,16));
 	// var_dump($md5_params);
 	 if ($md5_params!=$signature){return array ('false' => false,'r' => "签名验证失败");}
	}
	else {
		return array ('false' => false,'r' => "签名为空");
	}
	return array ('false' => true,'r' => "签名验证成功");
}
$obj = new stdClass ();
if(verification_signature ( $_POST )['false']==false)
{
	$obj ->status = 500;
	$obj ->r = verification_signature ( $_POST )['r'];
	CommonUtil::return_info($obj);
	die;
}

define('ROLE_INITIATOR',"0") ; 
define('ROLE_INVESTOR', "1") ; 
define('IMG_DOMAIN', "http://img.cisdaq.com/") ; 
define('BP_DOMAIN', "http://bp.cisdaq.com/") ; 
define('BP_URL', "http://www.cisdaq.com/bp_viewer/get_bp1.php?key=") ;
define('ACCESSKEY', "5CQmagmT27DNXYhsmVewntOpd9VLGD8sC5c02ptg") ; 
define('SECRETKEY', "lQm-akaIb3JOS-WAlBycTXF_95hx7JEd9s88ARk5") ; 

  function is_exist_mobile($mobile)
  {
     $sql="select is_effect from cixi_user where mobile='".$mobile."'";
     $user=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ, 1);
      if(!empty($user) ) 
      {
      		$is_effect=$user->is_effect;
            return $is_effect;
      }
      else
      
      {return '2';}
  }
  function is_exist_mobile_user($mobile)
  {
     $sql="select 1 from cixi_user where mobile='".$mobile."'";
     $user=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ, 1);
      if(!empty($user) ) 
      {
          return true;
      }
      return false;
  }
  //验证手机号码
function check_mobile($mobile)
{
  if(!empty($mobile) && !preg_match("/^1[34578]\d{9}$/",$mobile))
  {
    return false;
  }
  else{
     return true;
  }
}
//验证密码
function check_pwd($pwd){
  if(!preg_match("/^[A-Za-z0-9]{6,12}$/",$pwd)){
    return false;
  }
  else{
    return true;
  }
}
//获取项目版本号
function check_version($deal_id){
    $sql="select version from cixi_deal where id=".$deal_id;
    $result=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ, 1);
    if(!empty($result))
    {
      $version=$result->version;
      return $version;
    }else{
      return false;
    }   
}
?>
