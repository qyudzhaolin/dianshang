//  ###################################################
//	公共文件，修改需谨慎 sunerju
//  ###################################################

// 验证手机号是否输入及格式是否正确
function checkMobile(id,respond_field){
	var mobile=trim(get_input_value(id));
	if(mobile==''){
		$('#'+id).addClass('warning');
		$('#'+respond_field).html('请输入手机号');
		return false;
	}else{
		if(/^1[34578]\d{9}$/.test(mobile)){
			$('#'+id).removeClass('warning');
			$('#'+respond_field).html('');
			return true;
		}else{
			$('#'+id).addClass('warning');
			$('#'+respond_field).html('请输入正确的手机号码');
			return false;
		}
	}
}
// 登录密码验证，不需要验证格式，只验证数据是否输入
function checkLoginPwd(id,respond_field){
	var pwd=trim(get_input_value(id));
	if(pwd == ''){
		$('#'+respond_field).html('请输入密码');
		$('#'+id).addClass('warning');
		return false;
	}else{
		$('#'+respond_field).html('');
		$('#'+id).removeClass('warning');
		return true;
	}
}
// 验证密码是否输入及格式是否正确
function checkPwd(id,respond_field){
	var pwd=trim(get_input_value(id));
	if(pwd == ''){
		$('#'+respond_field).html('密码不能为空');
		$('#'+id).addClass('warning');
		return false;
	}
//	if(/^(?=.*[0-9].*)(?=.*[A-Z].*)(?=.*[a-z].*).{6,12}$/.test(pwd)){
	if(/^(?=.*[0-9].*)(?=.*[A-Za-z].*)[0-9a-zA-Z]{6,12}$/.test(pwd)){
		$('#'+respond_field).html('');
		$('#'+id).removeClass('warning');
		return true;
	}else{
		$('#'+respond_field).html('密码应由6-12位数字和字母组成');
		$('#'+id).addClass('warning');
		return false;
	}
}
// 验证再次输入的密码是否输入及格式是否正确
function checkPwdConfirm(id,idConfirm,respond_field){
	var pwd_value = get_input_value(id);
	var pwd_confirm_value = get_input_value(idConfirm);
	if(pwd_confirm_value != pwd_value){
		$('#'+respond_field).html('密码输入不一致');
		$('#'+idConfirm).addClass('warning');
		return false;
	}else{
		$('#'+respond_field).html('');
		$('#'+idConfirm).removeClass('warning');
		return true;
	}
}
// 验证真是姓名是否输入及格式是否正确
function checkUsername(id,respond_field){
	var user_name=trim(get_input_value(id));
	if(user_name==''){
		$('#'+respond_field).html('请输入您的真实姓名');
		$('#'+id).addClass('warning');
		return false;
	}else{
		$('#'+respond_field).html('');
		$('#'+id).removeClass('warning');
		return true;
	}

}
// 验证图片验证码是否输入及格式是否正确
function checkCode(id,respond_field){
	var verify_code=trim(get_input_value(id));
	if(verify_code==''){
		$('#'+respond_field).html('请输入图形验证码');
		$('#'+id).addClass('warning');
		return false;
	}
	if(/^\d{4}$/.test(verify_code)){
		$('#'+respond_field).html('');
		$('#'+id).removeClass('warning');
		return true;
	}else{
		$('#'+respond_field).html('输入的图形验证码错误');
		$('#'+id).addClass('warning');
		return false;
	}
}
// 验证短信验证码是否输入及格式是否正确
function checkSms(id,respond_field){
	var sms = trim(get_input_value(id));
	if(sms==''){
		$('#'+respond_field).html('请输入短信验证码');
		$('#'+id).addClass('warning');
		return false;
	}
	if(/^\d{6}$/.test(sms)){
		$('#'+respond_field).html('');
		$('#'+id).removeClass('warning');
		return true;
	}else{
		$('#'+respond_field).html('输入的短信验证码错误');
		$('#'+id).addClass('warning');
		return false;
	}
}
// 更新验证码
function updateCode(id,code_flag){
	var timenow = new Date().getTime();
	var url=APP_ROOT+"/index.php?ctl=user&act="+code_flag+"&rand="+timenow;
	$("#"+id).attr('src',url);
}
// 回车提交事件
function enterSubmit(id,button){
	$('#'+id).keydown(function(e){
        var curKey = e.which;
        if(curKey == 13){
        	$("#"+button).click();
        	return false;
        }
    });
}