var focus_div=0;//1注册；2登陆
function check_field()
{   clear_login();
    focus_div=1;
    var all_check=checkAll('register');
    if (!all_check) {return;};
    var name_check=checkUserName();
    if (!name_check) {return;};
    var mobile_check=checkPhoneNumreg("mobile","check_register_msg");
    if (!mobile_check) {return;};
    var pwd_check=checkPwdreg("user_pwd","check_register_msg");
    if (!pwd_check) {return;};
    var img_check=img_verify_check("img_verify","check_register_msg");
    if (!img_check) {return;};
     var code_check=checkCode("message","check_register_msg");
    if (!code_check) {return;};

    var agreement_check=checkAgreement();
    if (!agreement_check) {return;}

    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=register_check";
 
    var query = new Object();
    query.user_name = get_input_value('user_name');
    query.mobile = get_input_value('mobile');
    query.user_pwd = get_input_value('user_pwd');
    query.verify_code = get_input_value('message');
    query.img_verify = get_input_value('img_verify');
    
    var ajaxTimeoutTest =$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".reg_btn").attr('disabled', 'disabled');
                $("#check_register_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#check_register_msg").html('提交失败');
    　　　　}
                 $(".reg_btn").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {
                $('.reg_win1').hide();
                $('.reg_win2').show();
            }
            else
            {
                if(register_msg_hide()&&focus_div==LOGIN){
                        return;
                }
                $('#check_register_msg').html(ajaxobj.info);
                if(ajaxobj.field=='mobile'){
                        $('#mobile').addClass('warnning');
                }else{
                    if(ajaxobj.field=='verify_code'){
                        $('#message').addClass('warnning');
                    }else{
                        $('#img_verify').addClass('warnning');
                    } 
                }    
            }
        },
        error:function(ajaxobj)
        {
           $('#check_register_msg').html('提交失败');
        }
    });
}

function check_field_login()
{   clear_register();
    focus_div=2;
    var all_check=checkAll('login');
    if (!all_check) {return;};
    var mobile_login=checkPhoneNum("mobile_login","check_login_msg");
     if (!mobile_login) {return;};
    var user_pwd_login_check=checkPwd("user_pwd_login","check_login_msg");
    if (!user_pwd_login_check) {return;};

    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=do_login";
    var query = new Object();
    query.mobile_login =get_input_value('mobile_login'); 
    query.user_pwd_login =get_input_value('user_pwd_login');
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".login_btn").attr('disabled', 'disabled');
                $("#login_form_error").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#login_form_error").html('提交失败');
    　　　　}
                 $(".login_btn").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
        	{   
                if(ajaxobj.is_review==1){
                    if(ajaxobj.user_type==1){
                     location.href =APP_ROOT+"/deals/";
                    }else{
                     location.href =APP_ROOT+"/investors/";
                    }
                }else{
                     location.href =APP_ROOT+"/home/";
                }
        		   
                    $('#login_form_error').html('登录成功');
              }
            else
            {
               if(login_msg_hide()&&focus_div==REGISTER){
                        return;
                    }    
               $('#login_form_error').html(ajaxobj.info); 
               if(ajaxobj.field=='mobile_login'){
                        $('#mobile_login').addClass('warnning');
                }else{
                    if(ajaxobj.field=='user_pwd_login'){
                        $('#user_pwd_login').addClass('warnning');
                    } 
                }      
            }
        },
        error:function(ajaxobj)
        {
            $('#login_form_error').html('提交失败');  
        }
    });
}
function img_verify_check(id,response_field){
    var img_verify=trim(get_input_value(id));
    if(img_verify==''){
        $('#'+response_field).html('请输入图形验证码');
        $('#'+id).addClass('warnning');
        return false;
    }
    if(/^\d{4}$/.test(img_verify)){
       $('#'+response_field).html('');
       $('#'+id).removeClass('warnning');
        return true;
    }else{
         
        $('#'+response_field).html('图形验证码格式不正确');
        $('#'+id).addClass('warnning');
        return false;
    }
}
function check_img_verify(source){
    clear_login();
    if(source=='sms'){
        var mobile_check=checkPhoneNumreg("mobile","check_register_msg");
        if (!mobile_check) {return;};
    }
    /*if (!img_verify_check("img_verify","check_register_msg")) {return;};*/
    var img_verify=trim(get_input_value("img_verify"));
    if(img_verify==""){
        if(source=='sms'){
            $('#check_register_msg').html('请输入图形验证码');
            $('#img_verify').addClass('warnning');
        }
        return;
    }else{
        if(/^\d{4}$/.test(img_verify)){
           $('#check_register_msg').html('');
           $('#img_verify').removeClass('warnning');
        }else{
            $('#check_register_msg').html('图形验证码格式不正确');
            $('#img_verify').addClass('warnning');
            return;
        }
    }
    
    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=check_img_verify";
    var query = new Object();
    query.img_verify =get_input_value('img_verify'); 
    query.verify_name ='verify'; 
    if(source=='sms'){
        query.mobile = get_input_value('mobile');
        query.source = 'sms';
    }
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
               // $(".submit").attr('disabled', 'disabled');
                //$("#check_register_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#check_register_msg").html('提交失败');
    　　　　}
                // $(".submit").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(source=='sms'){
                if(ajaxobj.status==1)
                {   
                  get_verify_code('register');
                }
                else
                {
                   if(check_focus()=='login_mobile' || check_focus()=='user_pwd_login' || check_focus()=='login_button'){
                       return;
                    }
                    if(ajaxobj.status==2)
                    {
                        $('#img_verify').addClass('warnning');
                        
                    }else{
                        $('#mobile').addClass('warnning');
                    }   
                    $('#check_register_msg').html(ajaxobj.info);         
                }
            }else{
                if(check_focus()=='login_mobile' || check_focus()=='user_pwd_login' || check_focus()=='login_button'){
                       return;
                }
                if(ajaxobj.status==2)
                {
                        $('#img_verify').addClass('warnning');
                        
                } 
                $('#check_register_msg').html(ajaxobj.info);
            }
            
        },
        error:function(ajaxobj)
        {
            $('#check_register_msg').html('提交失败');  
        }
    });
}
var validCode=true;
function get_verify_code(source){

    //校正验证码
    var time=60;

    
    if (source=='register') {
        var ajaxurl = APP_ROOT+"/app_api/v2/SMS.php";
    }else{
        var ajaxurl = APP_ROOT+"/app_api/v2/SMS.php";
    }
    var sms = new Object();
    if (source=='register') {
         var mobile =  get_input_value('mobile');
    }else{
         var mobile =  get_input_value('get_pwd_mobile');
    }
    
    sms.mobile = mobile;
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:sms,
        type: "POST",

        success: function(ajaxobj){
            if(ajaxobj.status==200)
            {
                if (source=='register') {
                    $('#btn_verify_code').attr("onclick", "");
                }else{
                    $('#get_pwd_btn_verify_code').attr("onclick", "");
                }
                
                $("#check_register_msg").html('');
                $("#get_pwd_second_msg").html('');
               
                if (source=='register') {
                    var code=$('#btn_verify_code');
                }else{
                    var code=$('#get_pwd_btn_verify_code');
                }
                if (validCode) {
                    validCode=false;
                   
                    var timer=setInterval(function  () {
                    time--;
                    code.addClass('col');
                    code.html(time+"秒后重新发送");
                    if (time==0) {
                        clearInterval(timer);
                        code.removeClass('col');
                        code.html("获取短信验证码");
                        //$('#btn_verify_code').removeAttr("disabled");
                        if (source=='register') {
                            $('#btn_verify_code').attr("onclick", "javascript:check_img_verify('sms');");
                        }else{
                            $('#get_pwd_btn_verify_code').attr("onclick", "javascript:get_verify_code('get_pwd');");
                        }
                        
                        validCode=true;
                    }
                    },1000)
                }
                else{
                    return;
                }         
            }
            else
            {
                if (source=='register') {
                    $("#check_register_msg").html(ajaxobj.r);  
                }else{
                    $("#get_pwd_second_msg").html(ajaxobj.r);  
                }
                          
            }
        },
        error:function(ajaxobj)
        {
            if (source=='register') {
                 $("#check_register_msg").html('获取短信验证码失败');
            }else{
                 $("#get_pwd_second_msg").html('获取短信验证码失败');     
            }
           
        }
    });
}

function checkPhoneNum(id,response_field){
    var mobile=trim(get_input_value(id));
    if(mobile==''){
        $('#login_form_error').html('请输入手机号');
        $('#mobile_login').addClass('warnning');
        return false;
    }else{
        if(/^1[34578]\d{9}$/.test(mobile)){
       
           $('#login_form_error').html('');
            return true;
        }else{
            $('#login_form_error').html('手机号格式不正确');
            $('#mobile_login').addClass('warnning');
            return false;
        }
    }
    
}
function checkAll(source){
    if ('register'==source) {
        if(get_input_value('user_name')==''&&get_input_value('mobile')==''&& get_input_value('user_pwd')==''&& get_input_value('img_verify')==''&& get_input_value('message')==''){
        $('#check_register_msg').html('请填写内容');
        $('#user_name').addClass('warnning');
        $('#mobile').addClass('warnning');
        $('#user_pwd').addClass('warnning');
        $('#img_verify').addClass('warnning');
        $('#message').addClass('warnning');
        return false;
        }else{
            return true;
        }
    }else{
        if(get_input_value('mobile_login')==''&& get_input_value('user_pwd_login')==''){
        $('#login_form_error').html('请填写内容');
        $('#mobile_login').addClass('warnning');
        $('#user_pwd_login').addClass('warnning');
        return false;
        }else{
            return true;
        }
    }
    
}
function checkPhoneNumreg(id,response_field){
    var mobile=trim(get_input_value(id));
    if(mobile==''){
        $('#'+response_field).html('请输入手机号');
            $('#'+id).addClass('warnning');
            return false;
    }else{
        if(/^1[34578]\d{9}$/.test(mobile)){
           $('#'+response_field).html('');
            return true;
        }else{
            $('#'+response_field).html('手机号格式不正确');
            $('#'+id).addClass('warnning');
            return false;
        }
    }
    
}
function checkUserName(){
    var user_name=trim(get_input_value('user_name'));
    if(user_name==''){
        $('#check_register_msg').html('请输入真实姓名');
            $('#user_name').addClass('warnning');
            return false;
    }else{
        /*if(/^1[34578]\d{9}$/.test(mobile)){
           $('#check_register_msg').html('');
            return true;
        }else{
            $('#check_register_msg').html('手机号格式不正确');
            $('#mobile').addClass('warnning');
            return false;
        }*/
        return true;
    }
    
}
function checkPwd(id,response_field){
     var pwd=trim(get_input_value(id));
     if(pwd==''){
        $('#login_form_error').html('请输入密码');
        $('#user_pwd_login').addClass('warnning');
         return false;
     }
     if(/^[A-Za-z0-9]{6,12}$/.test(pwd)){
       $('#login_form_error').html('');
       $('#user_pwd_login').removeClass('warnning');
        return true;
    }else{
        $('#login_form_error').html('请保证密码在6到12位且只包含数字和字母');
        $('#user_pwd_login').addClass('warnning');
         return false;
    }
}
function checkPwdreg(id,response_field){
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
        $('#'+response_field).html('请保证密码在6到12位且只包含数字和字母');
        $('#'+response_field).addClass('warnning');
         return false;
    }
}
function confirm_pwd(user_pwd,user_pwd_confirm,response_field){
        var user_pwd_value=get_input_value(user_pwd);
        var user_pwd_confirm_value=get_input_value(user_pwd_confirm);
        if(user_pwd_confirm_value!=user_pwd_value){

            $('#'+response_field).html('密码不一致');
            $('#user_pwd_confirm').addClass('warnning');
             return false;
        }else{
            $('#'+response_field).html('');
            $('#user_pwd_confirm').removeClass('warnning');
            return true;
        }
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
        $('#'+response_field).html('验证码格式错误');
        $('#'+id).addClass('warnning');
        return false;
    }
}
function do_register(){ 
    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=do_register";
    var query = new Object();
    query.user_name = get_input_value('user_name');
    query.mobile = get_input_value('mobile');
    query.user_pwd = get_input_value('user_pwd');
    query.user_type = $('input:radio[name=user_type]:checked').val();
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".btn").attr('disabled', 'disabled');
                $("#login_form_role_error").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#login_form_role_error").html('提交失败');
    　　　　}
                 $(".btn").removeAttr('disabled');
            },
        success: function(ajaxobj){
           if(ajaxobj.status==1)
            {
                  if(query.user_type==1){
                    location.href =APP_ROOT+"/choice/";
                    }else{
                    $('.reg_win2').hide();
                    $('.confirm_success').show();
                    location.href =APP_ROOT+"/home/";
                    }
            }
            else
            {
                $('#login_form_role_error').html(ajaxobj.info); 
             }
        },
        error:function(ajaxobj)
        {
           $('#login_form_role_error').html('提交失败'); 
        }
    });
}

function checkAgreement(){

    var class_name=$('#read_agree').attr("class");
    if(class_name=="unchecked"){
        $('#check_register_msg').html('请阅读并同意磁斯达克协议');
        $('#checkbox').addClass('warnning');
        return false;
    }else{
        return true;
    }
} 


function close_btn(){
 $('.reg_win1,.gray_box').hide();
 $('.loginregister').html('');
 $('form').find("input[type=text],input[type=password]").removeClass("warnning");
}


function read_agree(){
    if(validCode){
        $('#read_agree').addClass('unchecked');
         // return false;
         validCode=false;
    }else{
 
        $('#read_agree').removeClass('unchecked');
        validCode=true;
    }
 
}

//回车提交事件
$(document).ready(function(){
    $('input[name=mobile_login],input[name=user_pwd_login]').keydown(function(e){
        var curKey = e.which;
        if(curKey == 13){
        $("#login_button").click();
        return false;
        }
    });
}); 
//找回密码回车提交事件第一步
$(document).ready(function(){
    $('input[name=get_pwd_mobile],input[name=img_verify_pwd]').keydown(function(e){
        var curKey = e.which;
        if(curKey == 13){
        $("#get_pwd_first_next").click();
        return false;
        }
    });
}); 

//找回密码回车提交事件第二步
$(document).ready(function(){
    $('input[name=get_pwd_message]').keydown(function(e){
        var curKey = e.which;
        if(curKey == 13){
        $("#get_pwd_second_next").click();
        return false;
        }
    });
}); 
//找回密码回车提交事件第三步
$(document).ready(function(){
      $('input[name=get_pwd_user_pwd],input[name=user_pwd_confirm]').keydown(function(e){
        var curKey = e.which;
        if(curKey == 13){
        $("#get_pwd_third_next").click();
        return false;
        }
    });
}); 
 
function update_img_verify(){
    clear_login();
    var timenow = new Date().getTime();
    var url=APP_ROOT+"/index.php?ctl=user&act=get_img_verify"+"&rand="+timenow;
    $("#verify").attr('src',url);
}
function show_img_verify(){
    clear_login();
    var mobile_check=checkPhoneNumreg("mobile","check_register_msg");
    if (!mobile_check) {return;};
    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=check_user_exist";
    var query = new Object();
    query.mobile = get_input_value('mobile');
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $('#btn_verify_code').attr("onclick", "");
                $("#check_register_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            $("#check_register_msg").html('');
            $('#btn_verify_code').attr("onclick", "javascript:show_img_verify();");
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#check_register_msg").html('提交失败');
    　　　　}
            },
        success: function(ajaxobj){
           if(ajaxobj.status==1)
            {
                if(check_focus()=='login_mobile' || check_focus()=='user_pwd_login' || check_focus()=='login_button'){
                   return;
                }
            }
            else
            {
                if(check_focus()=='login_mobile' || check_focus()=='user_pwd_login' || check_focus()=='login_button'){
                   return;
                }
                $('#check_register_msg').html(ajaxobj.info);
             }
        },
        error:function(ajaxobj)
        {
           $('#check_register_msg').html('提交失败'); 
        }
    });
}
function hide_img_verify(){
    $('.yzm').hide();
    $("#img_verify").val('');
    $(".reg_win").css({'zIndex':1000});
}
function last_step(){
    $('.reg_win2').hide();
    $('#check_register_msg').html('');
    $('.reg_win1').show();
}

function check_mobile_login_php(field)
{
    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=check_user_exist";
    var query = new Object();

    if (field=='mobile_login') {
        var mobile=trim(get_input_value("mobile_login"));
        if(mobile==''){
            return;
        }else{
            if(/^1[34578]\d{9}$/.test(mobile)){
               $('#login_form_error').html('');
            }else{
                $('#login_form_error').html('手机号格式不正确');
                $('#mobile_login').addClass('warnning');
                return;
            }
        }
        query.mobile =get_input_value('mobile_login');
    }else{
        var mobile=trim(get_input_value("mobile"));
        if(mobile==''){
            return;
        }else{
            if(/^1[34578]\d{9}$/.test(mobile)){
               $('#check_register_msg').html('');
            }else{
                $('#check_register_msg').html('手机号格式不正确');
                $('#mobile').addClass('warnning');
                return;
            }
        }
        query.mobile =get_input_value('mobile');
    }
 
    $.ajax({ 
        url: ajaxurl,
        dataType: "json",
        data:query,
        type: "POST",

        success: function(ajaxobj){
            if(field=='mobile_login')
            {   if(ajaxobj.status==1){
                    if(login_msg_hide()){
                        return;
                    }
                    $('#login_form_error').html('手机号未注册');
                    $('#'+field).addClass('warnning');
                }else{
                    if(ajaxobj.data.error==4){
                        if(login_msg_hide()){
                            return;
                        }
                        $('#login_form_error').html(ajaxobj.info);
                        $('#'+field).addClass('warnning');
                    }else{
                        $('#login_form_error').html('');
                        $('#'+field).removeClass('warnning');
                    }
                }
                 
            }else{
                if(ajaxobj.status==1){
                    //$('#check_register_msg').html('');
                    $('#'+field).removeClass('warnning');
                }else{
                    if(register_msg_hide()){
                        return;
                    }
                    if(ajaxobj.data.error==4||ajaxobj.data.error==3){
                        $('#check_register_msg').html('手机号已注册');
                        $('#'+field).addClass('warnning');
                    }else{
                        $('#check_register_msg').html(ajaxobj.info);
                        $('#'+field).removeClass('warnning');
                    }
                }
            }
        },
        error:function(ajaxobj)
        {
            $('#login_form_error').html('提交失败');  
        }
    });
}
function check_message_php()
{
    var verify_code=trim(get_input_value("message"));
    if(verify_code==''){
        return;
    }
    if(/^\d{6}$/.test(verify_code)){
        $('#check_register_msg').html('');
        $('#message').removeClass('warnning');
    }else{
        $('#check_register_msg').html('验证码格式错误');
        $('#message').addClass('warnning');
        return;
    }

    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=check_user_message";
    var query = new Object();
    query.verify_code = get_input_value('message');
    query.mobile = get_input_value('mobile');
   
  
    $.ajax({ 
        url: ajaxurl,
        dataType: "json",
        data:query,
        type: "POST",
        success: function(ajaxobj){
            if(ajaxobj.status==0)
            {   
                if(register_msg_hide()){
                        return;
                }
                $('#check_register_msg').html(ajaxobj.info);
                $('#message').addClass('warnning');
            }else{
                 $('#check_register_msg').html('');
                 $('#message').removeClass('warnning');
            }
        },
        error:function(ajaxobj)
        {
            $('#check_register_msg').html('提交失败');  
        }
    });
}
function clear_login(){
    $('#login_form_error').html('');
    $('#mobile_login').removeClass('warnning');
    $('#user_pwd_login').removeClass('warnning');
}
function clear_register(){
    $('#check_register_msg').html('');
    $('#user_name').removeClass('warnning');
    $('#mobile').removeClass('warnning');
    $('#user_pwd').removeClass('warnning');
    $('#img_verify').removeClass('warnning');
    $('#message').removeClass('warnning');
}
function check_focus(){
    var focus_field=$(':focus').attr("id");
    return focus_field;
}
function login_msg_hide(){
    if(focus_div==1&&check_focus()!='login_mobile' && check_focus()!='user_pwd_login' && check_focus()!='login_button'){
       return true;
    }else{
        return false;
    }
}
function register_msg_hide(){
    if(focus_div==2&&check_focus()!='mobile' && check_focus()!='user_pwd' && check_focus()!='message'&& check_focus()!='img_verify'&& check_focus()!='register_button' ){
        return true;
    }else{
        return false;
    }
}
((function(){
    $('input[name=mobile_login],input[name=user_pwd_login],[id=login_button]').live('focus',function(){
        clear_register();
    }); 
    $('input[name=mobile],input[name=user_pwd],input[name=img_verify],[id=btn_verify_code],[id=register_button],input[name=message]').live('focus',function(){
       clear_login();
    }); 
    $('input[name=mobile_login]').bind('blur',function(){
        check_mobile_login_php('mobile_login');
    });
    $('input[name=user_pwd_login]').bind('blur',function(){
        var pwd=trim(get_input_value("user_pwd_login"));
         if(pwd==''){
             return;
         }
         if(/^[A-Za-z0-9]{6,12}$/.test(pwd)){
           $('#login_form_error').html('');
           $('#user_pwd_login').removeClass('warnning');
        }else{
            $('#login_form_error').html('请保证密码在6到12位且只包含数字和字母');
            $('#user_pwd_login').addClass('warnning');
        }
    });
    $('input[name=mobile]').bind('blur',function(){
       check_mobile_login_php('mobile');
    });
    $('input[name=user_pwd]').bind('blur',function(){
        var pwd=trim(get_input_value("user_pwd"));
         if(pwd==''){
             return;
         }
         if(/^[A-Za-z0-9]{6,12}$/.test(pwd)){
           $('#check_register_msg').html('');
           $('#user_pwd').removeClass('warnning');
        }else{
            $('#check_register_msg').html('请保证密码在6到12位且只包含数字和字母');
            $('#user_pwd').addClass('warnning');
        }
    });
    $('input[name=message]').bind('blur',function(){
        check_message_php();
    });
    $('input[name=img_verify]').bind('blur',function(){
        check_img_verify('img_verify');
    });

})());
function update_img_verify_pwd(){
    var timenow = new Date().getTime();
    var url=APP_ROOT+"/index.php?ctl=user&act=get_pwd_img_verify"+"&rand="+timenow;
    $("#get_pwd_verify").attr('src',url);
}
function get_pwd(){
    clear_login();
    var mobile=get_input_value('mobile_login');
    if (mobile!="") {
        window.location.href=APP_ROOT+"/index.php?ctl=user&act=get_pwd"+"&mobile="+mobile;
    }else{
        window.location.href=APP_ROOT+"/index.php?ctl=user&act=get_pwd";
    }
    
}
function get_pwd_first_next()
{  
    if (get_input_value("get_pwd_mobile")==""&&get_input_value("img_verify_pwd")=="") {
        $('#get_pwd_first_msg').html('请填写内容');
        $('#get_pwd_mobile').addClass('warnning');
        $('#img_verify_pwd').addClass('warnning');
        return;
    };
    var mobile_check=checkPhoneNumreg("get_pwd_mobile","get_pwd_first_msg");
    if (!mobile_check) {return;};
    var img_check=img_verify_check("img_verify_pwd","get_pwd_first_msg");
    if (!img_check) {return;};
    
    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=get_pwd_second";
 
    var query = new Object();
    query.mobile = get_input_value('get_pwd_mobile');
    query.img_verify = get_input_value('img_verify_pwd');
    
    var ajaxTimeoutTest =$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".get_pwd_first").attr('disabled', 'disabled');
                $("#get_pwd_first_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#get_pwd_first_msg").html('提交失败');
    　　　　}
                 $(".get_pwd_first").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {
                $('.gain_msg_box1').hide();
                $(".retrieve_box ul li").eq(0).removeClass().addClass('finished');
                $(".retrieve_box ul li").eq(1).addClass('cur');                
                $('.gain_msg_box2').show();
            }
            else
            {
                if (ajaxobj.field=='get_pwd_mobile') {
                    $("#get_pwd_mobile").addClass('warnning');
                }else{
                    $("#img_verify_pwd").addClass('warnning');
                }
                $('#get_pwd_first_msg').html(ajaxobj.info);
                
            }
        },
        error:function(ajaxobj)
        {
           $('#get_pwd_first_msg').html('提交失败');
        }
    });
}
function get_pwd_second_next()
{  
    var code_check=checkCode("get_pwd_message","get_pwd_second_msg");
    if (!code_check) {return;};
    
    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=get_pwd_third";
 
    var query = new Object();
    query.sms_code = get_input_value('get_pwd_message');
    query.mobile = get_input_value('get_pwd_mobile');
    
    var ajaxTimeoutTest =$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".get_pwd_second").attr('disabled', 'disabled');
                $("#get_pwd_second_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#get_pwd_second_msg").html('提交失败');
    　　　　}
                 $(".get_pwd_second").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1 )
            {
                $('.gain_msg_box2').hide();
                $(".retrieve_box ul li").eq(0).removeClass().addClass('finished');
                $(".retrieve_box ul li").eq(1).removeClass().addClass('finished');
                $(".retrieve_box ul li").eq(2).addClass('cur');
                $('.gain_msg_box3').show();
            }
            else
            {
                $("#get_pwd_message").addClass('warnning');
                $('#get_pwd_second_msg').html(ajaxobj.info);
                
            }
        },
        error:function(ajaxobj)
        {
           $('#get_pwd_second_msg').html('提交失败');
        }
    });
}
function get_pwd_third_next()
{  
    var user_pwd=get_input_value('get_pwd_user_pwd');
    var user_pwd_confirm=get_input_value('user_pwd_confirm');
    if (trim(user_pwd)==""&&trim(user_pwd_confirm)=="") {
        $('#get_pwd_third_msg').html('请填写内容');
        $('#get_pwd_user_pwd').addClass('warnning');
        $('#user_pwd_confirm').addClass('warnning');
        return;
    };
    var user_pwd_check=checkPwdreg("get_pwd_user_pwd","get_pwd_third_msg");
    if (!user_pwd_check) {return;};
    var user_pwd_confirm_check=checkPwdreg("user_pwd_confirm","get_pwd_third_msg");
    if (!user_pwd_confirm_check) {return;};
    if (trim(user_pwd)!=trim(user_pwd_confirm)) {
        $('#get_pwd_third_msg').html('密码不一致');
        $('#get_pwd_user_pwd').addClass('warnning');
        $('#user_pwd_confirm').addClass('warnning');
        return;
    };
    
    var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=reset_pwd";
 
    var query = new Object();
    query.mobile = get_input_value('get_pwd_mobile');
    query.user_pwd = user_pwd;
    query.user_pwd_confirm = user_pwd_confirm;
    
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
                $('.gain_msg_box3').hide();
                $(".retrieve_box ul li").removeClass().addClass('finished');
                $('.gain_msg_box4').show();
            }
            else
            {
                $('#get_pwd_third_msg').html(ajaxobj.info);
                
            }
        },
        error:function(ajaxobj)
        {
           $('#get_pwd_third_msg').html('提交失败');
        }
    });
}
$(function(){
    $('.get_pwd input').focus(function(event) {
        /* Act on the event */
        $(this).attr({
            placeholder: ' '
        });
    });
    $('#get_pwd_user_pwd').blur(function(event) {
        /* Act on the event */
        $(this).attr({
            placeholder: '请输入6至12位数字或字母'
        });
    });
    $('#user_pwd_confirm').blur(function(event) {
        /* Act on the event */
        $(this).attr({
            placeholder: '请确认新密码'
        });
    }); 
})