<?php
		require_once('base.php');
		$obj = new stdClass;
		$obj->status = 500;
		$mobile = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
		$business_type = isset($_POST['business_type'])? trim($_POST['business_type']):NULL;
		if(is_null($mobile))
		{
			$obj->r = "请输入手机号码";
			CommonUtil::return_info($obj);
			return;
		}
		if(is_null($business_type))
		{
			$obj->r = "business_type 错误";
			CommonUtil::return_info($obj);
			return;
		}

		if(is_exist_mobile($mobile)=='1'&&1==$business_type){
			$obj->r = "该手机号码已注册";
			CommonUtil::return_info($obj);
			return;
		}
		if(is_exist_mobile($mobile)=='0'&&1==$business_type){
			$obj->r = "该手机号码已经禁用";
			CommonUtil::return_info($obj);
			return;
		}
		if (is_exist_mobile_user($mobile)==false&&2==$business_type)
		{
			$obj->r = "手机号没有注册过";
			  CommonUtil::return_info($obj);
			return;
		}
		
		
		
		//查看上次发送时间
			$time=time();

			$sql_select="select send_time from cixi_deal_msg_list where mobile_num = ? order by send_time asc";//最早的时间在上
			$para_select = array($mobile);
			$result_select = PdbcTemplate::query($sql_select,$para_select);
			if(!empty($result_select)){
				$msg_count=count($result_select);
				//最大时间差
				//$max_time_difference=$time-$result_select[$msg_count-1]->send_time;
				//$max_time_difference%1800;
				$time_difference=$time-$result_select[0]->send_time;//最早的时间
				$time_difference_late=$time-$result_select[$msg_count-1]->send_time;//最晚的时间
				
				if(1==$msg_count){
					if($time_difference_late<30){
						//30秒内只能一条
						$obj->r = "同一号码同一签名内容30秒内只能发送一条";
						CommonUtil::return_info($obj);
						return;
					}
				}
				if(2==$msg_count){
					if($time_difference_late<30){
						//30秒内只能一条
						$obj->r = "同一号码同一签名内容30秒内只能发送一条";
						CommonUtil::return_info($obj);
						return;
					}
					elseif($time_difference<60){
						//30秒内只能一条
						$obj->r = "同一号码同一签名内容60秒内只能发送2条";
						CommonUtil::return_info($obj);
						return;
					}
				}
				if(3==$msg_count){
					if($time_difference_late<30){
						//30秒内只能一条
						$obj->r = "同一号码同一签名内容30秒内只能发送一条";
						CommonUtil::return_info($obj);
						return;
					}
					elseif($time_difference<60){
						//30秒内只能一条
						$obj->r = "同一号码同一签名内容60秒内只能发送2条";
						CommonUtil::return_info($obj);
						return;
					}
					elseif($time_difference<1800){
						//30秒内只能一条
						$obj->r = "同一号码同一签名内容30分钟内不能超过3条";
						CommonUtil::return_info($obj);
						return;
					}else{
						$sql_del=" delete from  cixi_deal_msg_list where mobile_num = ?";
						$para_del = array($mobile);
						$result_del = PdbcTemplate::execute($sql_del, $para_del);
					}
				}
			}

		 //1.生成随机数
		$code = rand(100000,999999);
		$data ="尊敬的用户，您的验证码为：" . $code." 请勿告知他人" ;
		//$_SESSION['code'] = $code;

		 //2.发送短信
		$post_data = array();
		$post_data['account'] = iconv('GB2312', 'GB2312','csdake');
		$post_data['pswd'] = iconv('GB2312', 'GB2312','Tch123456');
		$post_data['mobile'] =$mobile;
		$post_data['msg']=mb_convert_encoding("$data",'UTF-8', 'auto');
		$url='http://222.73.117.156/msg/HttpBatchSendSM?'; 
		$o="";
		foreach ($post_data as $k=>$v)
		{
		   $o.= "$k=".urlencode($v)."&";
		}
		$post_data=substr($o,0,-1);
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//把结果返回，而非直接输出
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

		$result = curl_exec($ch);
		$result_array = explode(',',$result);
		if ($result_array[1]!='0') {
			$obj->status = $result_array[1];
			$obj->r = "获取验证码失败";
			CommonUtil::return_info($obj);
			return;
		}
		if(!empty($result_array))
		{
			/*
			$sql_del=" delete from  cixi_deal_msg_list where mobile_num = ?";
			$para_del = array($mobile);
			$result_del = PdbcTemplate::execute($sql_del, $para_del);*/
			
			$sql_sms = " insert into cixi_deal_msg_list 
						(code, mobile_num, send_time, is_success)
						values
						(?,?,?,?)
						";
			
			$para_sms = array($code,$mobile,$time,$result_array[1]);
			$result_sms = PdbcTemplate::execute($sql_sms, $para_sms);

			if(true == $result_sms[0]&&0==$result_array[1])
			{  
				$obj->status = 200;
			}
			else
			{
				$obj->r = "获取验证码失败";
			}
		}
		CommonUtil::return_info($obj);
?>