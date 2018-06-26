

function checkLogo(obj1,obj2,obj3){
	if(get_input_value(obj1)==''){
		$('#'+obj1+'_msg').removeClass('gray');
		$('#'+obj1+'_msg').html('&nbsp;&nbsp;请上传您的名片');
		$('#'+obj2+'').addClass('logored');
		$('#'+obj3+'').focus();
		return false;
	}else{
		$('#'+obj1+'_msg').html('点击图片重新上传');
		$('#'+obj2+'').removeClass('logored');
		return true;
	}

}
 

function user_review(){
	var personal_msg_json='[{"name":"user_name","required":true,"error_id":"user_name_msg","error_msg":"请填写真实姓名"},\
		{"name":"user_name","max_len":6,"error_id":"user_name_msg","error_msg":"请确保真实姓名在6个字以内"}]';
	var res=csdk_check(personal_msg_json);
 
	var img_check=checkLogo('img_card_logo','personal_card','card_img');
 	if (!img_check   || !res) {return;}
	var ajaxurl = APP_ROOT+"/index.php?ctl=account&act=user_update";
	var query = new Object();    
	query.user_name =get_input_value('user_name');
 	query.img_card_logo =get_input_value('img_card_logo');
	 
	var ajaxTimeoutTest=$.ajax({ 
		url: ajaxurl,
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$(".next").attr('disabled', 'disabled');
			$("#error_msg").html('提交中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status=='timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#error_msg").html('提交失败');
			}
			$(".next").removeAttr('disabled');
		},
		success: function(ajaxobj){
			if(ajaxobj.status==1)
			{  
     		    window.location.href = APP_ROOT+"/account/review";
     		     
			}
			else
			{
				$('#error_msg').html(ajaxobj.info);
			}
		},
		error:function(ajaxobj)
		{
			$('#error_msg').html('提交失败');
		}
	});

}


function del_event(o)
{
	$(o).parent().remove();
}
function checkPwdreg(id,response_field){
    var pwd=trim(get_input_value(id));
    if(pwd==''){
       $('#'+response_field).html('请输入密码');
       $('#'+id).addClass('warnning');
        return false;
    }
    if(/^(?=.*[0-9].*)(?=.*[A-Za-z].*)[0-9a-zA-Z]{6,12}$/.test(pwd)){
      $('#'+response_field).html('');
      $('#'+id).removeClass('warnning');
     
       return true;
   }else{
       $('#'+response_field).html('请保证密码在6到12位且只包含数字和字母');
       $('#'+id).addClass('warnning');
        return false;
   }
}
function checkPwdreg_old(id,response_field){
    var pwd=trim(get_input_value(id));
    if(pwd==''){
       $('#'+response_field).html('请输入密码');
       $('#'+id).addClass('warnning');
        return false;
    }
    if(/^[A-Za-z0-9]{6,12}$/.test(pwd)){
      $('#'+response_field).html('');
      $('#'+id).removeClass('warnning');
     
       return true;
   }else{
       $('#'+response_field).html('请保证密码在6到12位');
       $('#'+id).addClass('warnning');
        return false;
   }
}
 

function reset_pwd(){
	var user_old_pwd_db=get_input_value('user_old_pwd_db');
	var user_old_pwd=get_input_value('user_old_pwd');
    var user_new_pwd=get_input_value('user_new_pwd');
    var user_new_pwd_confirm=get_input_value('user_new_pwd_confirm');
    var user_old_pwd_check=checkPwdreg_old("user_old_pwd","user_old_pwd_msg");
    if (!user_old_pwd_check) {return;};
    var user_old_pwd_md5=$.md5(user_old_pwd);
    if (trim(user_old_pwd_md5)!=trim(user_old_pwd_db)) {
        $('#user_old_pwd_msg').html('旧密码输入错误');
        $('#user_old_pwd').addClass('warnning');
        return;
    };
    var user_new_pwd_check=checkPwdreg("user_new_pwd","user_new_pwd_msg");
    if (!user_new_pwd_check) {return;};
   
    if (trim(user_old_pwd)==trim(user_new_pwd)) {
        $('#get_pwd_third_msg').html('新密码不可以和旧密码相同');
        $('#user_old_pwd').addClass('warnning');
        $('#user_new_pwd').addClass('warnning');
        return;
    }else
    	{
    	$('#get_pwd_third_msg').html('');
    	};
    var user_new_pwd_confirm_check=checkPwdreg("user_new_pwd_confirm","user_new_pwd_confirm_msg");
    if (!user_new_pwd_confirm_check) {return;};
 
    if (trim(user_new_pwd)!=trim(user_new_pwd_confirm)) {
        $('#get_pwd_third_msg').html('密码不一致');
        $('#user_new_pwd').addClass('warnning');
        $('#user_new_pwd_confirm').addClass('warnning');
        return;
    }else
    	{
    	$('#get_pwd_third_msg').html('');
    	};
    var ajaxurl = APP_ROOT+"/index.php?ctl=account&act=user_pwd_update";
	var query = new Object();    
	query.user_old_pwd =get_input_value('user_old_pwd');
	query.user_old_pwd_db =get_input_value('user_old_pwd_db');
	query.user_new_pwd =get_input_value('user_new_pwd'); 
	query.user_new_pwd_confirm =get_input_value('user_new_pwd_confirm'); 
	var ajaxTimeoutTest=$.ajax({ 
		url: ajaxurl,
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$(".next").attr('disabled', 'disabled');
			$("#error_msg").html('提交中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status=='timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#error_msg").html('提交失败');
			}
			$(".next").removeAttr('disabled');
		},
		success: function(ajaxobj){
			if(ajaxobj.status==1)
			{  		
					 alert("密码修改成功！");
					 window.location.href = APP_ROOT+"/user/signin";
			}
			else
			{
				$('#error_msg').html(ajaxobj.info);
			}
		},
		error:function(ajaxobj)
		{
			$('#error_msg').html('提交失败');
		}
	});

}

function checkCode(id,response_field){
    var verify_code=trim(get_input_value(id));
    if(verify_code==''){
        $('#'+response_field).html('请输入验证码');
        $('#'+id).addClass('warnning');
        return false;
    }
    if(/^\d{6}$/.test(verify_code)){
        $('#'+response_field).html('');
        $('#'+id).removeClass('warnning');
       return true;
    }else{
        $('#'+response_field).html('验证码错误');
        $('#'+id).addClass('warnning');
        return false;
    }
}
function checkPhoneNum(id,response_field){
    var mobile=trim(get_input_value(id));
    if(mobile==''){
    	  $('#'+response_field).html('请输入手机号');
          $('#'+id).addClass('warnning');
          return false;
    }else{
        if(/^1[34578]\d{9}$/.test(mobile)){
        	 $('#'+response_field).html('');
             $('#'+id).removeClass('warnning');
            return true;
        }else{
        	  $('#'+response_field).html('手机号格式错误');
              $('#'+id).addClass('warnning');
            return false;
        }
    }
    
}
 $('#get_news_mobile').on('focus',function(){
	 //var new_mobile=checkPhoneNum('get_news_mobile',"get_news_mobile_msg");
 })
 $('#get_news_mobile').on('blur',function(){
	 var new_mobile=checkPhoneNum('get_news_mobile',"get_news_mobile_msg");
 })
function bind_mobile()
{  
    var old_mobile=checkPhoneNum('get_pwd_mobile',"get_pwd_mobile_msg");
    var old_code_check=checkCode("get_pwd_message","get_pwd_msg");
     
    var new_mobile=checkPhoneNum('get_news_mobile',"get_news_mobile_msg");
    var new_code_check=checkCode("get_new_message","get_pwd_second_msg");
    if (!old_mobile || !old_code_check|| !new_mobile || !new_code_check ) {return;}
    var ajaxurl = APP_ROOT+"/index.php?ctl=account&act=reset_pwd";
 
    var query = new Object();
    query.old_sms_code = get_input_value('get_pwd_message');
    query.old_mobile = get_input_value('get_pwd_mobile');
    query.new_sms_code = get_input_value('get_new_message');
    query.new_mobile = get_input_value('get_news_mobile');
    
    var ajaxTimeoutTest =$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".get_pwd_third").attr('disabled', 'disabled');
                $("#get_pwd_third_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#get_pwd_third_msg").html('提交失败');
    　　　　}
                 $(".get_pwd_third").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {
            	 alert("换绑手机号成功！");
            	 window.location.href = APP_ROOT+"/user/signin";
            	 
            }
            else
            { 
                $(ajaxobj.error_msg).html(ajaxobj.info);
                
            }
        },
        error:function(ajaxobj)
        {
           $('#get_pwd_third_msg').html('提交失败');
        }
    });
}

$(function(){
	$("#get_pwd_message").blur(function(){
		checkCode("get_pwd_message","get_pwd_msg");
	});
	$("#get_new_message").blur(function(){
		checkCode("get_new_message","get_pwd_second_msg");
	});
});
