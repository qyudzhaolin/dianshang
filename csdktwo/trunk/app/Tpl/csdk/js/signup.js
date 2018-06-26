function checkAll(isSms){
	if(!checkMobile("mobile",'mobile_error_msg')) return false;
	if(!checkPwd("pwd",'pwd_error_msg')) return false;
	if(!checkPwdConfirm("pwd","pwd_confirm",'pwd_confirm_error_msg')) return false;
	if(!checkUsername("username",'username_error_msg')) return false;
	if(!checkCode("code",'code_error_msg')) return false;
	if(!checkSms("sms",'sms_error_msg')) return false;
	return true;
}

//发送短信验证码验证
function sendSmsCheck(){
	if(!checkMobile("mobile",'mobile_error_msg')) return false;
	if(!checkCode("code",'code_error_msg')) return false;
	var query = new Object();
	query.code = $("#code").val();
	var ajaxTimeoutTest = $.ajax({
		url: APP_ROOT+"/index.php?ctl=user&act=check_code",
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$("#sms_btn").attr("onclick", "");
			$("#sms_error_msg").html('发送中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status == 'timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#code_error_msg").html('图形验证码验证超时');
				$("#sms_btn").attr("onclick", "javascript:sendSmsCheck();");
			}
		},
		success: function(data){
			// 验证码错误，不发送
			if(data.status == 0){
				$("#sms_btn").attr("onclick", "javascript:sendSmsCheck();");
				$("#sms_error_msg").html('');
				$("#code_error_msg").html('图形验证码错误');
				$('#code').addClass('warning');
				updateCode('codeImg','get_signup_code');
			}else{
				var config = {
						'mobile':'mobile',
						'business_type':1,
						'button':'sms_btn',
						'respond_field':'sms_error_msg',
						'click_event':"javascript:sendSmsCheck();",
						'validCode':true,
				};
				get_verify_code(config);
			}
		},
		error:function(ajaxobj){
			$("#code_error_msg").html('图形验证码验证失败');
			$("#sms_btn").attr("onclick", "javascript:sendSmsCheck();");
		}
	});

}

function signup(){
	if(!checkAll()) return;
	
	var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=dosignup";
	var query = new Object();
	query.mobile = get_input_value('mobile');
	query.pwd = get_input_value('pwd');
	query.pwd_confirm = get_input_value('pwd_confirm');
	query.username = get_input_value('username');
	query.code = get_input_value('code');
	query.sms = get_input_value('sms');

	var ajaxTimeoutTest = $.ajax({
		url: ajaxurl,
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$("#signup").attr('disabled', 'disabled');
			$('#sms').next().html('注册中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status == 'timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$('#sms').next().html('网络超时');
			}
			$("#signup").removeAttr('disabled')
		},
		success: function(data){
			$('#sms').next().html('');
			if(data.status == 1){
				$('#sms').next().html(data.info);
				window.location.href = data.data;
			}else{
				$('#'+data.data).next().html(data.info);
				$('#'+data.data).addClass('warning');
			}
		},
		error:function(ajaxobj){
			$('#sms').next().html('注册失败');
		}
	});
}

$(function(){
	$('#mobile').blur(function(event) {
		checkMobile("mobile",'mobile_error_msg');
	});

	$('#pwd').blur(function(event) {
		checkPwd("pwd",'pwd_error_msg');
	});

	$('#pwd_confirm').blur(function(event) {
		checkPwdConfirm("pwd","pwd_confirm",'pwd_confirm_error_msg');
	});

	$('#username').blur(function(event) {
		checkUsername("username",'username_error_msg');
	});

	$('#code').blur(function(event) {
		checkCode("code",'code_error_msg');
	});

	$('#sms').blur(function(event) {
		checkSms("sms",'sms_error_msg');
	});
	
//	enterSubmit('sms','signup');
})