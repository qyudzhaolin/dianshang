<?php
// require_once('../api_v1/conf/conf_db.php');
// require_once('../api_v1/conf/conf_redis.php');
// require_once('../api_v1/fun/CommonUtil.php');
// require_once('../api_v1/fun/PdbcTemplate.php');
// require_once('../api_v1/fun/RedisTemplate.php');

// require_once('/conf/conf_db.php');
define('RUN_ENV',isset($_SERVER['RUN_ENV'])?$_SERVER['RUN_ENV']:'develop');
require_once('./conf/'.RUN_ENV.'/conf_db.php');

//require_once(APP_PATH."/api_v1/conf/conf_redis.php");
require_once("./fun/CommonUtil.php");
require_once("./fun/PdbcTemplate.php");
require_once("./fun/waf.php");
//require_once(APP_PATH."/api_v1/fun/RedisTemplate.php");


define('ROLE_INITIATOR',"0") ; 
define('ROLE_INVESTOR', "1") ; 
define('IMG_DOMAIN', "http://img.cisdaq.com/") ; 
define('BP_DOMAIN', "http://bp.cisdaq.com/") ; 

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
