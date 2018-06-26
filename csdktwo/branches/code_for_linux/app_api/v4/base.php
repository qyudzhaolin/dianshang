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
 * | Version: 2.0
 * +----------------------------------------------------------------------
 * |
 */
require_once ('../function/Public.php');

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
