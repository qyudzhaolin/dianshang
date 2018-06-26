function tab_scale(){
	var oLine_div = document.querySelector('.Password_02 .spot_box div');
	oLine_div.style.width = '190px';
}
function sendSmsCheck(){
	if(!checkMobile('mobile','mobile_error_msg1')) return;
	if(!checkCode('code','code_error_msg1')) return;
	var config = {
			'mobile':'mobile',
			'business_type':2,
			'button':'get_sms_code',
			'respond_field':'sms_error_msg1',
			'click_event':"javascript:sendSmsCheck();",
			'validCode':true,
	};
	get_verify_code(config);
}
function step1(){
	if(!checkMobile('mobile','mobile_error_msg1')) return;
	if(!checkCode('code','code_error_msg1')) return;
	if(!checkSms('sms','sms_error_msg1')) return;
	
	var query = new Object();
	query.mobile = get_input_value("mobile");
	query.code = get_input_value("code");
	query.sms = get_input_value("sms");
	
	var ajaxTimeoutTest = $.ajax({
		url: APP_ROOT+"/index.php?ctl=user&act=dofindpass_step1",
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$("#step1").attr('disabled', 'disabled');
			$("#sms_error_msg1").html('提交中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status == 'timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#sms_error_msg1").html('网络超时');
			}
			$("#step1").removeAttr('disabled')
		},
		success: function(data){
			$("#sms_error_msg1").html('');
			if(data.status == 1){
				$('.Sign_In_from').css('display','none');
				$('.Sign_In_from').eq(1).css({'display':'block'})
				setTimeout(function(){
					tab_scale()
				},333)
			}else{
				if(data.status == 99) updateCode('codeImg','get_findpassword_code');
				$('#'+data.data+'_error_msg1').html(data.info);
				$('#'+data.data).addClass('warning');
			}
		},
		error:function(ajaxobj){
			$("#sms_error_msg1").html('验证失败');
		}
	});
}

function step2(){
	if(!checkMobile('mobile','pwd_confirm_error_msg2')) return;
	if(!checkCode('code','pwd_confirm_error_msg2')) return;
	if(!checkSms('sms','pwd_confirm_error_msg2')) return;
	if(!checkPwd('pwd','pwd_error_msg2')) return;
	if(!checkPwdConfirm('pwd','pwd_confirm','pwd_confirm_error_msg2')) return;
	
	var query = new Object();
	query.mobile = get_input_value("mobile");
	query.code = get_input_value("code");
	query.sms = get_input_value("sms");
	query.pwd = get_input_value("pwd");
	query.pwd_confirm = get_input_value("pwd_confirm");
	
	var ajaxTimeoutTest = $.ajax({
		url: APP_ROOT+"/index.php?ctl=user&act=dofindpass_step2",
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$("#step2").attr('disabled', 'disabled');
			$("#pwd_confirm_error_msg2").html('验证中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status == 'timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#pwd_confirm_error_msg2").html('网络超时');
			}
			$("#step2").removeAttr('disabled')
		},
		success: function(data){
			$("#pwd_confirm_error_msg2").html('');
			if(data.status == 1){
				$('.Sign_In_from').css('display','none');
				$('.Sign_In_from').eq(2).css({'display':'block'})
			}else{
				if(data.data){
					$('#'+data.data+'_error_msg1').html(data.info);
					$('#'+data.data).addClass('warning');
				}else{
					$("#pwd_confirm_error_msg2").html(data.info);
				}
			}
		},
		error:function(ajaxobj){
			$("#pwd_confirm_error_msg2").html('重置失败');
		}
	});
}

$(function(){
	$('#mobile').blur(function(event) {
		checkMobile('mobile','mobile_error_msg1');
	});
	$('#code').blur(function(event) {
		checkCode('code','code_error_msg1');
	});
	$('#sms').blur(function(event) {
		checkSms('sms','sms_error_msg1');
	});
	
	$('#pwd').blur(function(event) {
		checkPwd('pwd','pwd_error_msg2');
	});

	$('#pwd_confirm').blur(function(event) {
		checkPwdConfirm('pwd','pwd_confirm','pwd_confirm_error_msg2');
	});
	
//	enterSubmit("sms","step1");
//	enterSubmit("pwd_confirm","step2");
})