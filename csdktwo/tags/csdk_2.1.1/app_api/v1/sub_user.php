<?php
require_once('base.php');
require_once('/fun/session.php');
//5.2	请求融资阶段列表
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);


function make_sub_json($sub_user)
{

}

 	$sign_sn		= trim($_POST['sign_sn']);
 	$uid			= isset($_POST['uid'])? trim($_POST['uid']):NULL;
 	$type 			= isset($_POST['type'])? trim($_POST['type']):NULL;//add_user or modify_pwd or del_user

 	if($type != "add_user" && $type != "modify_pwd" && $type!= "del_user"&& $type!= "sub_user_info")
 	{
		$obj->r = "不存在对应业务类型";
		CommonUtil::return_info($obj);
    	return;
 	}
	
 	$sql="select mobile,sub_user_pwd from cixi_user where id= ?";
   	$para = array($uid);
   	$user=  PdbcTemplate::query($sql,$para,PDO::FETCH_OBJ, 1);
   	if (empty($user))
    {
    	$obj->r = "不存在对应用户";
		CommonUtil::return_info($obj);
    	return;
    }
	
   	if($type=="sub_user_info")
   	{
   		$sql="select * from cixi_sub_user where user_id= ?";
   		$para = array($uid);
   		$sub_user=  PdbcTemplate::query($sql,$para);
   		if (empty($sub_user))
    	 {     
    	 		//$obj->status = 555;
    			//$obj->r = "";
				CommonUtil::return_info($obj);
    			return;
    	 }
    	 else
    	 {     
    			$obj_mobile=array();
				foreach ($sub_user as $k => $v) {
					 	$ary = new stdClass;	
					    $ary->mobile= is_null($v->sub_mobile) ? "" : $v->sub_mobile;
					    array_push($obj_mobile, $ary) ;
				}

    			$obj->status = 200;
				$data['sub_user_pwd']=$user->sub_user_pwd;	
				$data['obj_mobile']=$obj_mobile;
				$obj->data=$data;
    	 	    CommonUtil::return_info($obj);
    		   return;
    	 }
   	}
	if($type=="modify_pwd")
	{
		$sql="select * from cixi_sub_user where user_id= ?";
   		$para = array($uid);
   		$sub_user=  PdbcTemplate::query($sql,$para);
   		if (empty($sub_user))
    	 {     
    			$obj->r = "请先新建子账号再修改密码";
				CommonUtil::return_info($obj);
    			return;
    	 }

			$sub_user_pwd     = isset($_POST['sub_user_pwd'])? trim($_POST['sub_user_pwd']):NULL;
			if (is_null($sub_user_pwd)) {
				$obj->r = "密码不能为空";
				CommonUtil::return_info($obj);
    			return;
			}
			if (check_pwd($sub_user_pwd)==false)
		    {
		    	$obj->r = "密码格式不正确";
				CommonUtil::return_info($obj);
		    	return;
		    }
			$sql_update   ="update cixi_user set sub_user_pwd= ? where id= ?";
			$para_update[]=$sub_user_pwd;
			$para_update[]=$uid;
	   		$result =  PdbcTemplate::execute($sql_update,$para_update);
	    	if($result[0]===false){
				$obj->r = "修改子账号密码失败";
				CommonUtil::return_info($obj);
				return;
			}
			else{
				$obj->status = 200;
				CommonUtil::return_info($obj);
				return;
			}
    }

	$mobile		= isset($_POST['mobile'])? trim($_POST['mobile']):"";


  	if($type=="del_user")
    {

			 if($mobile=="")
    		 {
    		 	$obj->r = "解绑手机号不得为空";
				CommonUtil::return_info($obj);
				return;
    		 }

    	$sql="select * from cixi_sub_user where user_id= {$uid} and sub_mobile ={$mobile}";
   		$sub_user=  PdbcTemplate::query($sql);
		if (empty($sub_user))
    	{
    			$obj->r = "不存在对应用户";
				CommonUtil::return_info($obj);
    			return;
    	 }
    	 else
    	 {
			    $sql="delete from cixi_sub_user where user_id= {$uid} and sub_mobile ={$mobile}";
   			    $result =  PdbcTemplate::execute($sql);
			    $obj->status = 200;
				//$data['user_pwd']=$sub_user->user_pwd;	
				if($result[0]===false){
					$obj->r = "删除子账号密码失败";
					CommonUtil::return_info($obj);
					return;
				}
				else
				{
					//$obj->data=$data;
					CommonUtil::return_info($obj);
					return;
				}
    	 }
    }
    
    if($type=="add_user")
    {
    	 $mobiles  =   explode('_',$mobile);
    	 $mobiles  =	array_filter ($mobiles);
    	 if(sizeof($mobiles)==0)
    	 {
    	 	$obj->r = "至少要填写一个手机号";
			CommonUtil::return_info($obj);
			return;
    	 }

    	 if(sizeof($mobiles)>3)
    	 {
    	 	$obj->r = "手机号过多";
			CommonUtil::return_info($obj);
			return;
    	 }

		if (count($mobiles) != count(array_unique($mobiles))) {   
				  $obj->r = "禁止添加重复手机号";
					CommonUtil::return_info($obj);
					return;
		} 

         foreach ($mobiles as $v){
          if(is_exist_mobile($v)==true)
				{
					$obj->r = "已经存在对应手机号";
					CommonUtil::return_info($obj);
					return;
				}
		if(check_mobile($v)==false)
				{
					$obj->r = "手机号格式不正确";
					CommonUtil::return_info($obj);
					return;
				}
           }


  	    foreach ($mobiles as $v){
			$sql="insert into cixi_sub_user(user_id,sub_mobile) values ({$uid},{$v})";
			$result =  PdbcTemplate::execute($sql);
			if($result[0]===false){
					$obj->r = "增加新子账号失败";
					CommonUtil::return_info($obj);
					return;
				}
  	     }

			$password     = isset($_POST['sub_user_pwd'])? trim($_POST['sub_user_pwd']):"";
			if ($password !="") {
				if (check_pwd($password)==false)
			    {
			    	$obj->r = "密码格式不正确";
					CommonUtil::return_info($obj);
			    	return;
			    }
				$sql_update   ="update cixi_user set sub_user_pwd= ? where id= ?";
				$para_update[]=$password;
				$para_update[]=$uid;
		   		$result =  PdbcTemplate::execute($sql_update,$para_update);
		    	if($result[0]===false){
					$obj->r = "修改子账号密码失败";
					CommonUtil::return_info($obj);
					return;
				}
				else{
					$obj->status = 200;
					CommonUtil::return_info($obj);
					return;
				}
			}
	
  		 	$obj->status = 200;
			//$obj->data=$data;
			CommonUtil::return_info($obj);
			return;

	 }
?>