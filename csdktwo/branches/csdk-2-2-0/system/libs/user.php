<?php 
define("EMPTY_ERROR",1);  //未填写的错误
define("FORMAT_ERROR",2); //格式错误
define("EXIST_ERROR",3); //已存在的错误

define("ACCOUNT_NO_EXIST_ERROR",1); //帐户不存在
define("ACCOUNT_PASSWORD_ERROR",2); //帐户密码错误
define("ACCOUNT_NO_VERIFY_ERROR",3); //帐户未激活
define("SUB_USER_ERROR",3); //是否是子账号
	/**
	 * 处理会员登录
	 * @param $user_name_or_mobile 用户名或邮箱地址
	 * @param $user_pwd 密码
	 * 
	 */
	
	 /*是否存在*/
    function is_exist($table,$where)
    {  
        $sql="select 1 from {$table} where {$where}";
        $result= $GLOBALS['db']->getRow($sql,true);
        if(!$result)
          { 
            return false;
          }  
        return true;
    }

	function do_login_user($mobile,$user_pwd)
	{
		if($mobile=='')
		{
			$result['status'] = 0;
			$result['data']['field_name'] = 'mobile_login';
			$result['data']['error'] = EMPTY_ERROR;
			return $result;
		}

		if($user_pwd=='')
		{
			$result['status'] = 0;
			$result['data']['field_name'] = 'user_pwd_login';
			$result['data']['error'] = EMPTY_ERROR;
			return $result;
		}

		$sql="select *,cixi_user.id as id,cixi_user.user_pwd as user_pwd from ".DB_PREFIX."user  left join ".DB_PREFIX."sub_user
    		  on cixi_user.id=".DB_PREFIX."sub_user.user_id where "
    		  .DB_PREFIX."sub_user.sub_mobile='{$mobile}' or ".DB_PREFIX."user.mobile='{$mobile}'";
		$user_data = $GLOBALS['db']->getRow($sql);
		if(!$user_data)
		{	
			$result['status'] = 0;
			$result['data']['field_name'] = 'mobile_login';
			$result['data']['error'] = ACCOUNT_NO_EXIST_ERROR;
			return $result;
		}
		else
		{
			$result['user'] = $user_data;
			/*if($user_data['user_pwd'] != md5($user_pwd)&&$user_data['sub_user_pwd']!=$user_pwd)
			{
				$result['status'] = 0;
				$result['data']['field_name'] = 'user_pwd_login';
				$result['data']['error'] = ACCOUNT_PASSWORD_ERROR;
				return $result;
			}*/
			if($user_data['mobile']==$mobile&&$user_data['user_pwd'] != md5($user_pwd) || $user_data['sub_mobile']==$mobile&&$user_data['sub_user_pwd']!=$user_pwd)
			{
				$result['status'] = 0;
				$result['data']['field_name'] = 'user_pwd_login';
				$result['data']['error'] = ACCOUNT_PASSWORD_ERROR;
				return $result;
			}
			elseif($user_data['is_effect'] != 1)
			{
				$result['status'] = 0;
				$result['data']['field_name'] = 'mobile_login';
				$result['data']['error'] = ACCOUNT_NO_VERIFY_ERROR;
				return $result;
			}
			else{

				if($user_data['mobile']==$mobile)
				{
					$user_data['is_sub_user']=0;//主账号
					//$user_data['mobile']=$mobile;
				}
				else
				{
					$user_data['is_sub_user']=1;//子账号
					//$user_data['mobile']=$user_data['mobile'];
				}
				
				$user['is_sub_user'] = $user_data['is_sub_user'];
                $result1=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$user_data['id'],"SILENT");

                if(!$result1){ 
					$result['status'] = 0;
					$result['data']['field_name'] = 'user_pwd_login';
					$result['data']['error'] = SUB_USER_ERROR;
					return $result;
			    }

				es_session::set("user_info",$user_data);
				$GLOBALS['user_info'] = $user_data;
				//var_dump($GLOBALS['user_info']);
				$result['status'] = 1; 
				return $result;
			}
		}
	}
	
	/**
	 * 登出,返回 array('status'=>'',data=>'',msg=>'') msg存放整合接口返回的字符串
	 */
	function loginout_user()
	{
		$user_info = es_session::get("user_info");
		if(!$user_info)
		{
			return false;
		}
		else
		{
			if(intval($result['status'])==0)	
			{
				$result['status'] = 1;
			}			

			es_session::delete("user_info");
			return $result;
		}
	}
	

	/**
	 * 验证会员数据
	 */
	function check_user($field_name,$field_data)
	{		
		//开始数据验证
		$user_data[$field_name] = $field_data;
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		$mobile=trim($user_data['mobile']);
		if($field_name=='mobile'&&!check_mobile($mobile))
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	FORMAT_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
		}
		else if(is_exist(DB_PREFIX."user","mobile={$mobile}")
			||is_exist(DB_PREFIX."sub_user","sub_mobile={$mobile}"))
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;

			$sql="select cixi_user.is_effect as is_effect from ".DB_PREFIX."user  left join ".DB_PREFIX."sub_user
    		  on cixi_user.id=".DB_PREFIX."sub_user.user_id where "
    		  .DB_PREFIX."sub_user.sub_mobile='{$mobile}' or ".DB_PREFIX."user.mobile='{$mobile}'";
			$user_data = $GLOBALS['db']->getRow($sql);
			if($user_data['is_effect'] != 1)
			{
					$res['status'] = 0;
					$res['data']['field_name'] = 'mobile';
					$res['data']['error'] = 4;
			}
		}
		
		return $res;
	}
	/**
	 * 删除会员以及相关数据
	 * @param integer $id
	 */
	function delete_user($id)
	{
		
		$result = 1;
		//载入会员整合
		$integrate_code = trim(app_conf("INTEGRATE_CODE"));
		if($integrate_code!='')
		{
			$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
			if(file_exists($integrate_file))
			{
				require_once $integrate_file;
				$integrate_class = $integrate_code."_integrate";
				$integrate_obj = new $integrate_class;
			}	
		}
		if($integrate_obj)
		{
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id);			
			$result = $integrate_obj->delete_user($user_info);				
		}
		
		if($result>0)
		{

			//$GLOBALS['db']->query("delete from ".DB_PREFIX."user_consignee where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_log where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_refund where user_id = ".$id);
			//$GLOBALS['db']->query("delete from ".DB_PREFIX."user_weibo where user_id = ".$id);
			//$GLOBALS['db']->query("delete from ".DB_PREFIX."user_consignee where user_id = ".$id);
			
			//$GLOBLAS['db']->query("delete from ".DB_PREFIX."deal where user_id = ".$id); //不删除相关的项目记录
			
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_comment where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_focus_log where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_event where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_msg_list where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_order where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_event where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_support_log where user_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."payment_notice where user_id = ".$id);
			
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user where id =".$id); //删除会员			
		}
	}



?>