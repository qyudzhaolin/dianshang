function signin(){
	if(!checkMobile('mobile','mobile_error_msg')) return;
	if(!checkLoginPwd('pwd','pwd_error_msg')) return;
	
	var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=dosignin";
	var query = new Object();
	query.mobile = get_input_value('mobile');
	query.pwd = get_input_value('pwd');

	var ajaxTimeoutTest = $.ajax({
		url: ajaxurl,
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$("#signin").attr('disabled', 'disabled');
			$("#pwd_error_msg").html('正在登录...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status == 'timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#pwd_error_msg").html('网络超时');
			}
			$("#signin").removeAttr('disabled')
		},
		success: function(data){
			$('#pwd_error_msg').html('');
			$('#'+data.data+'_error_msg').html(data.info);
			if(data.data){
				$('#'+data.data).addClass('warning');
			}else{
				$('#pwd_error_msg').html(data.info);
			}
			if(data.status == 1){
				$backurl = $("input[name=backurl]").val();
				$backurl = $backurl ? $backurl : APP_ROOT+"/home/";
				window.location.href = $backurl;
			}
		},
		error:function(ajaxobj){
			$('#pwd_error_msg').html('登录失败');
		}
	});
}

$(function(){
	$('#mobile').keydown(function(event) {
		$('#pwd').val("");
	}).blur(function(event) {
		checkMobile('mobile','mobile_error_msg');
	});
	
	$('#pwd').blur(function(event) {
		checkLoginPwd('pwd','pwd_error_msg');
	});
	enterSubmit('pwd','signin');
})