

function checkLogo(obj1,obj2,obj3){
	if(get_input_value(obj1)==''){
		$('#'+obj1+'_msg').removeClass('gray');
		$('#'+obj1+'_msg').html('请添加头像');
		$('#'+obj2+'').addClass('logored');
		$('#'+obj3+'').focus();
		return false;
	}else{
		$('#'+obj1+'_msg').html('点击图片重新上传');
		$('#'+obj2+'').removeClass('logored');
		return true;
	}

}

//验证邮箱的格式
function checkEmail(){
    var patt= /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    //var deal_url=trim(document.getElementById(id).value);
    var email=trim(get_input_value('email'));
        if(patt.test(email) || email==""){
            $('#email_msg').html('');
            return true;
        }else{
            $('#email_msg').html('邮箱格式不正确');
            $('#email').addClass('warnning');
            $('#email').focus();
            return false;          
        }
}

function checkRegion(){
	if(trim(get_input_value('province'))==''||trim(get_input_value('city'))==''){
		$('#region_msg').html('必填');
		$('#province').addClass('warnning');
		$('#city').addClass('warnning');
		$('#province').focus();
		return false;
	}else{

		$('#region_msg').html('');
		$('#province').removeClass('warnning');
		$('#city').removeClass('warnning');
		return true;
	}
}
function checkRegion_rw(){
	if(trim(get_input_value('province_rw'))==''||trim(get_input_value('city_rw'))==''){
		$('#region_rw_msg').html('必填');
		$('#province_rw').addClass('warnning');
		$('#city_rw').addClass('warnning');
		$('#province_rw').focus();
		return false;
	}else{

		$('#region_rw_msg').html('');
		$('#province_rw').removeClass('warnning');
		$('#city_rw').removeClass('warnning');
		return true;
	}
}
function checkDegree(){
	if(trim(get_input_value('degree'))==''){
		$('#degree_msg').html('必填');
		$('#degree').addClass('warnning');
		$('#degree').focus();
		return false;
	}else{
		$('#degree_msg').html('');
		$('#degree').removeClass('warnning');
		return true;
	}
}
function checkDegree_rw(){
	if(trim(get_input_value('degree_rw'))==''){
		$('#degree_rw_msg').html('必填');
		$('#degree_rw').addClass('warnning');
		$('#degree_rw').focus();
		return false;
	}else{
		$('#degree_rw_msg').html('');
		$('#degree_rw').removeClass('warnning');
		return true;
	}
}

function personal_msg_next(){
	var personal_msg_json='[{"name":"email","max_len":30,"error_id":"email_msg","error_msg":"请确保内容在30个字以内"}]';
	var res=csdk_check(personal_msg_json);
	var region_check=checkRegion();
	var degree_check=checkDegree();
	var img_check=checkLogo('img_user_logo','personal_logo','user_img');
    var email_check = checkEmail();
	if (!img_check || !region_check|| !degree_check || !email_check || !res) {return;}
	var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=personal_update";
	var query = new Object();    
	query.img_user_logo =get_input_value('img_user_logo');
	query.province =get_input_value('province'); 
	query.city =get_input_value('city');
	query.degree =get_input_value('degree');
	query.email =get_input_value('email');
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
			{  		//alert(document.getElementById("img_src").src);
					alert("个人资料更新成功");
					
					window.location.href = APP_ROOT+"/home/";
					 
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

 
//在线网址
function checkOrgUrl(){
	var patt = /^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/;
 
	var org_url=trim(get_input_value('org_url'));
 
	if(patt.test(org_url) || org_url==""){
		$('#org_url_msg').html('');
		$('#org_url').removeClass('warnning');
		return true;
	}else{
		$('#org_url_msg').html('网址格式不正确');
		$('#org_url').addClass('warnning');
		$('#org_url').focus();
		return false;          
	}

}

//公司联系电话
function checkOrgTel(){
	var patt =/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$|(^(13[0-9]|15[0|3|6|7|8|9]|18[8|9])\d{8}$)/;
	var org_mobile=trim(get_input_value('org_mobile'));
 
	if(patt.test(org_mobile) || org_mobile==""){
		$('#org_mobile_msg').html('');
		$('#org_mobile').removeClass('warnning');
		return true;
	}else{
		$('#org_mobile_msg').html('联系电话格式不正确');
		$('#org_mobile').addClass('warnning');
		$('#org_mobile').focus();
		return false;          
	}

}
function investor_org_next(){  
	var organization_json='[{"name":"org_title","required":true,"error_id":"org_title_msg","error_msg":"请填写担任职务"},\
		{"name":"org_title","max_len":8,"required":true,"error_id":"org_title_msg","error_msg":"请确保担任职务在8个字以内"},\
		{"name":"org_name","required":true,"error_id":"org_name_msg","error_msg":"请填写所属公司名称"},\
		{"name":"org_name","max_len":20,"required":true,"error_id":"org_name_msg","error_msg":"请确保公司名称在20个字以内"}]';
	var res=csdk_check(organization_json);
	var url_check=checkOrgUrl();
	var tel_check=checkOrgTel();
	if ( !url_check || !tel_check || !res ) {return;}
	var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=investor_org_update";

	var query = new Object();    
	query.org_name =get_input_value('org_name');
	query.org_desc =get_input_value('org_desc');
	query.org_mobile =get_input_value('org_mobile');
	query.org_title =get_input_value('org_title');
	query.org_linkman =get_input_value('org_linkman');
	query.org_url =get_input_value('org_url');
	query.user_id =get_input_value('user_id');

	var ajaxTimeoutTest=$.ajax({ 
		url: ajaxurl,
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$(".next").attr('disabled', 'disabled');
			//alert("公司信息保存成功");
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
				$('#org_name').removeClass('warnning');
				$('#org_title').removeClass('warnning');
				$('#org_name_rw').val(query.org_name);
				$('#org_title_rw').val(query.org_title);
				alert("公司信息更新成功");
				//window.location.href = APP_ROOT+"/home/investor_org";
				
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

function personal_review(){
	var personal_msg_json='[{"name":"org_title_rw","required":true,"error_id":"org_title_rw_msg","error_msg":"请填写担任职务"},\
		{"name":"org_title_rw","max_len":8,"required":true,"error_id":"org_title_rw_msg","error_msg":"请确保担任职务在8个字以内"},\
		{"name":"org_name_rw","required":true,"error_id":"org_name_rw_msg","error_msg":"请填写所属公司名称"},\
		{"name":"org_name_rw","max_len":20,"required":true,"error_id":"org_name_rw_msg","error_msg":"请确保公司名称在20个字以内"}]';
	var res=csdk_check(personal_msg_json);
	var region_rw_check=checkRegion_rw();
	var degree_rw_check=checkDegree_rw();
	var img_rw_check=checkLogo('img_user_logo_rw','personal_logo_rw','user_img_rw');
	if (!img_rw_check || !region_rw_check|| !degree_rw_check  || !res) {return;}
	var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=personal_review";
	var query = new Object();    
	query.img_user_logo =get_input_value('img_user_logo_rw');
	query.province =get_input_value('province_rw'); 
	query.city =get_input_value('city_rw');
	query.degree =get_input_value('degree_rw');
	query.org_name =get_input_value('org_name_rw');
	query.org_title =get_input_value('org_title_rw');
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
					//alert("个人资料与公司信息更新成功");
				    $("#img_src").attr("src", document.getElementById("img_src_rw").src);
				    $("#img_user_logo").val(query.img_user_logo);
				    $('#img_user_logo_msg').html('点击图片重新上传');
				    $('#org_name').val(query.org_name);
				    $('#org_title').val(query.org_title);
				    $('#org_name_rw').removeClass('warnning');
					$('#org_title_rw').removeClass('warnning');
				    $('.form.bs').css('display','none');
				    $('.form.bs.active2.update').css('display','block')
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

function user_review(){
	var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=user_update";
	var query = new Object();    
	query.img_cardz_logo =get_input_value('img_cardz_logo');
	query.img_cardf_logo =get_input_value('img_cardf_logo'); 
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
     		    window.location.href = APP_ROOT+"/home/review";
     		     
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

 

function reset_pwd(){
	var user_old_pwd_db=get_input_value('user_old_pwd_db');
	var user_old_pwd=get_input_value('user_old_pwd');
    var user_new_pwd=get_input_value('user_new_pwd');
    var user_new_pwd_confirm=get_input_value('user_new_pwd_confirm');
    var user_old_pwd_check=checkPwdreg("user_old_pwd","user_old_pwd_msg");
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
    var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=user_pwd_update";
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
					 $('.form.bs').css('display','none');
				     $('.form.bs.pos').css('display','block');
				     $('#user_old_pwd').val('');
				     $('#user_new_pwd').val('');
				     $('#user_new_pwd_confirm').val('');
				     $('#user_old_pwd_db').val($.md5(user_new_pwd));
				     $('#get_pwd_third_msg').html('');
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
function bind_mobile()
{  
    var old_mobile=checkPhoneNum('get_pwd_mobile',"get_pwd_mobile_msg");
    var old_code_check=checkCode("get_pwd_message","get_pwd_msg");
     
    var new_mobile=checkPhoneNum('get_news_mobile',"get_news_mobile_msg");
    var new_code_check=checkCode("get_new_message","get_pwd_second_msg");
    if (!old_mobile || !old_code_check|| !new_mobile || !new_code_check ) {return;}
    var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=reset_pwd";
 
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
            	 window.location.href = APP_ROOT+"/home/bind_mobile";
            	 
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
