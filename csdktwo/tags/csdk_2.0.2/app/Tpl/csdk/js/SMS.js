 
var configN = {
		'mobile':'get_news_mobile',
		'business_type':1,
		'button':'get_pwd_btn_verify_code_new',
		'respond_field':'get_pwd_second_msg',
		'click_event':"javascript:get_verify_code('get_new');",
		 'validCode':true,
};
var configD = {
		'mobile':'get_pwd_mobile',
		'business_type':2,
		'button':'get_pwd_btn_verify_code',
		'respond_field':'get_pwd_msg',
		'click_event':"javascript:get_verify_code('get_pwd');",
		'validCode':true,
};
function get_verify_code(config){

	//校正验证码
	var ajaxurl = APP_ROOT+"/app_api/v4/SMS.php";

	// 兼容旧版引用方式
	if(config){
		if(config=='get_new'){
			var config = configN;
		}else if(config=='get_pwd'){
			var config = configD;
		}else{
			var config = config;
		}
	}else{
		var config = configD;
	}

	var sms = new Object();
	sms.mobile = get_input_value(config.mobile);
	sms.business_type = config.business_type;
	var ajaxTimeoutTest=$.ajax({ 
		url: ajaxurl,
		timeout:20000,
		dataType: "json",
		data:sms,
		type: "POST",
		beforeSend: function () {
			$("#"+config.respond_field).html('发送中...');
			$("#"+config.button).attr("onclick", "");
		},
		complete: function (XMLHttpRequest,status) {
			if(status == 'timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#"+config.respond_field).html('');
				sms_interval(config);
			}

		},
		success: function(ajaxobj){
			if(ajaxobj.status == 200){
				$("#"+config.respond_field).html('');
				sms_interval(config);
			}else{
				$("#"+config.respond_field).html(ajaxobj.r);
				$("#"+config.button).attr("onclick",config.click_event);
			}
		},
		error:function(ajaxobj){
			$("#"+config.respond_field).html('');
			sms_interval(config);
		}
	});
}
function sms_interval(config){
	
	var code = $("#"+config.button);
 
	if (config.validCode) {
		config.validCode = false;
		var time = 60;
		$(code).css({'color':'#cfcfcf','border':'1px solid #cfcfcf'});
		code.html(time+"秒后重新发送");
		var timer = setInterval(function(){
			time--;
			code.html(time+"秒后重新发送");

			if (time==0) {
				clearInterval(timer);
				code.html("获取短信验证码");
				code.attr("onclick",config.click_event);
				config.validCode = true;
				$(code).css({'color':'#dba775','border':'1px solid #dba775'})
			}
		},1000)
	}else{
		return;
	}
}