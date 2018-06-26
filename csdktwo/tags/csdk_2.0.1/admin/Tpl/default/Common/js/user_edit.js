//切换地区
$(document).ready(function(){	
		$("select[name='province']").bind("change",function(){
			load_city();
		});
});
	
function load_city()
{
		var id = $("select[name='province']").find("option:selected").attr("rel");
		
		var evalStr="regionConf.r"+id+".c";

		if(id==0)
		{
			var html = "<option value=''>请选择城市</option>";
		}
		else
		{
			var regionConfs=eval(evalStr);
			evalStr+=".";
			var html = "<option value=''>请选择城市</option>";
			for(var key in regionConfs)
			{
				html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
			}
		}
		$("select[name='city']").html(html);		
}
 
function get_input_value(id){
	var v=$('#'+id).val();
	var title=$('#'+id).attr('title');
	if(v==title)
	{
		return "";
	}
	return v;
}
//在线网址
function checkUrl(){
    var patt=/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\%&=]*)?$/i;
    //var deal_url=trim(document.getElementById(id).value);
    var org_url=trim(get_input_value('org_url'));
    if(trim(get_input_value('org_url'))==''){
        $('#org_url_msg').html('必填');
        $('#org_url').addClass('warnning');
        $('#org_url').focus();
        return false;
    }else{
        if(patt.test(org_url)){
            $('#org_url_msg').html('');
            return true;
        }else{
            $('#org_url_msg').html('在线网址格式不正确');
            $('#org_url').addClass('warnning');
            $('#org_url').focus();
            return false;          
        }

    }

}

//验证邮箱的格式
function checkEmail(){
    var patt= /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    //var deal_url=trim(document.getElementById(id).value);
    var org_url=trim(get_input_value('email'));
    if(trim(get_input_value('email'))==''){
    	 $('#email').removeClass('warnning');
    	 return true;
    }else{
        if(patt.test(org_url)){
            $('#email_msg').html('');
            return true;
        }else{
            $('#email_msg').html('邮箱格式不正确');
            $('#email').addClass('warnning');
            $('#email').focus();
            return false;          
        }

    }

}


function del_event(o)
{
	$(o).parent().remove();
}
function check_user_field(field_id,is_require,max_len,img_file_id){
	 var field=$("[name="+field_id+"]").val();
	 field=trim(field);
	 if (is_require==1&&field=='') {
	 	$("#"+field_id+"_msg").html('必填');
	 	if (img_file_id!='') {
	 		$("#"+img_file_id).addClass('warnning');
	 	}else{
	 		$("[name="+field_id+"]").addClass('warnning');
	 	}
        
        $("[name="+field_id+"]").focus();
        return false;
	 }else{
        if(max_len!=0&&field.length>max_len){
            $("#"+field_id+"_msg").html('请确保内容在'+max_len+'个字以内');
            $("[name="+field_id+"]").addClass('warnning');
            $("[name="+field_id+"]").focus();
            return false;
        }else{
            $("#"+field_id+"_msg").html('');
            return true;
        }
    }
}
function checkPwd(){
     var pwd=$("[name=user_pwd]").val();
	 pwd=trim(pwd);
     if(pwd==''){
         return true;
     }
     if(/^(?=.*[0-9].*)(?=.*[A-Za-z].*).{6,12}$/.test(pwd)){
       $('#user_pwd_msg').html('');
       $('#user_pwd').removeClass('warnning');
        return true;
    }else{
        $('#user_pwd_msg').html('请保证密码在6到12位且只包含数字和字母');
        $('#user_pwd').addClass('warnning');
         return false;
    }
}
//选择方向
function checkCate(){
    var cate_check=0;
    $("[name='cate_choose[]']").each(function(){
    if($(this).attr("checked")=='checked'){
        cate_check=cate_check+1;
    }
    });
    if (cate_check==0) {
        $('#cate_choose_msg').html('必选');
       // $("#cates").addClass('warnning');
        return false;
    }else{
        $('#cate_choose_msg').html('');
        return true;
    }
}
//选择方向
function checkPeriod(){
    var cate_check=0;
    $("[name='period_choose[]']").each(function(){
    if($(this).attr("checked")=='checked'){
        cate_check=cate_check+1;
    }
    });
    if (cate_check==0) {
        $('#period_choose_msg').html('必选');
       // $("#cates").addClass('warnning');
        return false;
    }else{
        $('#period_choose_msg').html('');
        return true;
    }
}

function add_user_check(){
	var add_user_json='[\
                      {"name":"mobile","mobile":1,"required":true,"error_id":"mobile_msg","error_msg":"手机号格式不正确"},\
                      {"name":"user_pwd","password":1,"required":true,"error_id":"user_pwd_msg","error_msg":"请保证密码在6到12位且必须含数字字母"},\
                      {"name":"user_name","max_len":6,"required":true,"error_id":"user_name_msg","error_msg":"请确保内容在6个字以内"} \
                      ]';
    var res=csdk_check(add_user_json);                  
    var mobile_repeat_check = check_repeate_mobile($("[name=mobile]").val());
    var email_check = checkEmail();
	if(!res || mobile_repeat_check || !email_check){
		return false;
	}else{
		return true;
	}
}
function edit_user_check(){
	var edit_user_json='[\
	    {"name":"mobile","mobile":1,"required":true,"error_id":"mobile_msg","error_msg":"手机号格式不正确"},\
	    {"name":"user_name","max_len":6,"required":true,"error_id":"user_name_msg","error_msg":"请确保内容在6个字以内"} \
	    ]';
	var res=csdk_check(edit_user_json);      
	var mobile_change_check = check_change_mobile($("[name=mobile]").val(),$("[name=id]").val());
	 var email_check = checkEmail();
	if(!res || mobile_change_check|| !email_check){
		return false;
	}else{
		return true;
	}
}

function modify_password_check(){
	var add_user_json='[\
                      {"name":"password","password":1,"required":true,"error_id":"password_msg","error_msg":"请保证密码在6到12位且必须含数字字母"},\
                      {"name":"confirm_password","required":true,"error_id":"confirm_password_msg","error_msg":"两次密码输入不一致"},\
                      ]';
    var res=csdk_check(add_user_json);  
    var password_check = check_confirm_password('password','confirm_password');
	if(!res || !password_check){
		return false;
	}else{
		return true;
	}
}

function check_confirm_password(password,confirm_password){
	var password_value = $('#'+password).val();
	var confirm_password_value = $('#'+confirm_password).val();
	if(password_value != '' && confirm_password_value == password_value){
		return true;
	}else{
		$('#'+confirm_password+'_msg').html('两次密码输入不一致');
        $('#'+confirm_password).addClass('warnning');
		return false;
	}
}

function check_repeate_mobile(mobile){
	var result = false;
	// 检查手机号是否为空，为空则不重复
	if(mobile == null || mobile == ''){
		return false;
	}
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=User&"+VAR_ACTION+"=check_mobile_repeat&mobile="+mobile, 
			data: "ajax=1",
			async: false,
			success: function(obj){
				var jsonObj = JSON.parse(obj);
				if(jsonObj.status == 1){
					result = true;
				}
			}
		});
	 
	if(result){
		$('#mobile_msg').html('该手机号已被注册！');
        $('#mobile').addClass('warnning');
		return true;
	}else{
		return false;
	}
}


function check_change_mobile(mobile,id){
	var result = false;
	// 检查手机号是否为空，为空则不重复
	if(mobile == null || mobile == ''){
		return false;
	}
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=User&"+VAR_ACTION+"=check_mobile_change&mobile="+mobile+"&id="+id, 
			data: "ajax=1",
			async: false,
			success: function(obj){
				var jsonObj = JSON.parse(obj);
				if(jsonObj.status == 1){
					result = true;
				}
			}
		});
	if(result){
		$('#mobile_msg').html('该手机号已被注册！');
        $('#mobile').addClass('warnning');
		return true;
	}else{
		return false;
	}
}



$(document).ready(function() {
 	$('input[type=text],input[type=password],textarea').live('blur',function(){
 		var v=$(this).val();
		if (v!=''){
			$(this).removeClass("warnning");
		}
	});
});
