function checkPhoneNum(mobile){
        var mobile1=trim($('#mobile1').val());
        var mobile2=trim($('#mobile2').val());
        var mobile3=trim($('#mobile3').val());
        var false_mobile=0;
        var mobile_str="";
        if((mobile1==mobile2&&mobile1!='')||(mobile1==mobile3&&mobile1!='')||(mobile2==mobile3&&mobile3!='')){
            $('#mobile_msg').html('不能重复添加手机号');
            return false;
        }else{
            $res=true;
        }   
        if (mobile3=='' && mobile2=='' &&mobile1=='') {
            $('#mobile_msg').html('请输入手机号');
            $('#mobile1').addClass('warnning');
            $('#mobile2').addClass('warnning');
            $('#mobile3').addClass('warnning');
            $('#mobile1').focus();
            return false;
        }
        if (mobile3==mobile) {
            $('#mobile_msg').html('子账号不能与主账号相同');
            $('#mobile3').addClass('warnning');
            $('#mobile3').focus();
            return false;
        }
        if ( mobile2==mobile) {
            $('#mobile_msg').html('子账号不能与主账号相同');
            $('#mobile2').addClass('warnning');
            $('#mobile2').focus();
            return false;
        }
        if (mobile1==mobile) {
            $('#mobile_msg').html('子账号不能与主账号相同');
            $('#mobile1').addClass('warnning');
            $('#mobile1').focus();
            return false;
        }
        if(!/^1[34578]\d{9}$/.test(mobile3)&&mobile3!=''){
            $('#mobile_msg').html('手机号格式不正确');
            $('#mobile3').addClass('warnning');
            $('#mobile1').removeClass("warnning");
            $('#mobile2').removeClass("warnning");
            $('#mobile3').focus();
            false_mobile=false_mobile+1;
            $res3=false;
        }else{
            if(mobile3!=''){
                mobile_str="  "+mobile3+mobile_str;
            }
            $('#mobile_msg').html('');
            $res3=true;
        }
        if(!/^1[34578]\d{9}$/.test(mobile2)&&mobile2!=''){
             
            $('#mobile_msg').html('手机号格式不正确');
            $('#mobile2').addClass('warnning');
            $('#mobile1').removeClass("warnning");
            $('#mobile3').removeClass("warnning");
            $('#mobile2').focus();
            false_mobile=false_mobile+1;
            $res2=false;
        }else{
            if(mobile2!=''){
                mobile_str="  "+mobile2+mobile_str;
            }
            if (false_mobile==0) {
                $('#mobile_msg').html('');
            }
            $res2=true;
        } 
        if(!/^1[34578]\d{9}$/.test(mobile1)&&mobile1!=''){
            $('#mobile_msg').html('手机号格式不正确');
            $('#mobile1').addClass('warnning');
            $('#mobile3').removeClass("warnning");
            $('#mobile2').removeClass("warnning");
            $('#mobile1').focus();
            false_mobile=false_mobile+1;
            $res1=false;
        }else{
            if(mobile1!=''){
                mobile_str="  "+mobile1+mobile_str;
            }
            if (false_mobile==0) {
                $('#mobile_msg').html('');
            }
            $res1=true;
        }
        if($res1&&$res2&&$res3){
            return mobile_str;
        }else{
            return false;
        }
}

function sub_account_next(user_type){
    sub_account_reset();

    var ajaxurl = APP_ROOT+"/index.php?ctl=subuser&act=investor_account_update";

    var query = new Object();    
    query.mobile1 =get_input_value('mobile1');
    query.mobile2 =get_input_value('mobile2');
    query.mobile3 =get_input_value('mobile3');
    query.user_pwd =get_input_value('user_pwd');
    
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".yes").attr('disabled', 'disabled');
                $("#error_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#error_msg").html('提交失败');
    　　　　}
                 $(".yes").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {  
                window.location.href = APP_ROOT+"/subuser/manage_account";
                
            }
            else
            {
                if(ajaxobj.data.error=='3'){
                    $('#error_msg').html('不能授权已存在的手机号');
                }else{
                   $('#error_msg').html(ajaxobj.info);
                }
                
            }
        },
        error:function(ajaxobj)
        {
           $('#error_msg').html('提交失败');
        }
    });

}

function del_sub_account(user_type){
    sub_account_reset();
    var m=3;
    for(var i=1;i<=3;i++){
        if($("#div_mobile"+i).is(":hidden")){
            if (document.getElementById("div_mobile"+i).style.display=='none') { 
                m=m-1;  
            }
            
        }
    }
    var ajaxurl = APP_ROOT+"/index.php?ctl=subuser&act=del_account";

    var query = new Object();    
    query.user_id =get_input_value('user_id');
    field=get_input_value('del_mobile_num');
    query.mobile =get_input_value(field);
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".del_btn").attr('disabled', 'disabled');
                $("#error_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#error_msg").html('提交失败');
    　　　　}
                 $(".del_btn").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {   
                $("#div_"+field).hide();
                $('#err_msg').html(''); 
                if(m==1){
                    if (user_type=='1') {
                        window.location.href = APP_ROOT+"/subuser/investor_account";
                    }else{
                        window.location.href = APP_ROOT+"/subuser/estp_account";
                    }
                }
                var extra_num=3-m+1;
                var n=m-1;
                $('#extra_num').html(extra_num);
                $('#n').html(n);
                $("#continue_add_button").removeClass('c9');
                $("#continue_add_button").attr('onclick', 'continue_add();');
            }
            else
            {
                $('#err_msg').html(ajaxobj.info); 
            }
        },
        error:function(ajaxobj)
        {
            $('#err_msg').html('提交失败');  
        }
    });

}
function check_add_mobile(mobile){
    var error_num=0;
    var mobile1="";
    var mobile2="";
    var len=$("input[name=mobile_add]").size();
    $("input[name=mobile_add]").each(function(index) {
        if($(this).val()==mobile){
        if(error_num==0){$(this).addClass('warnning');}
        $('#err_msg').html('子账号不能与主账号相同');
            error_num=error_num+1;
        }
        if(index==0){
            mobile1=$(this).val();
        }
        if(index==1){
            mobile2=$(this).val();
        }
        if(!/^1[34578]\d{9}$/.test(trim($(this).val()))&&trim($(this).val())!=''){
            $('#err_msg').html('手机号格式不正确');
            if(error_num==0){
                $(this).addClass('warnning');
                $(this).focus();
            }
            error_num=error_num+1;
        }
    });
    if(trim(mobile1)!=""&&trim(mobile2)!=""&&trim(mobile1)==trim(mobile2)){
        $('#err_msg').html('不能重复添加手机号');
        return false;
    }
    if(trim(mobile1)==""&&trim(mobile2)==""){
        $("input[name=mobile_add]").each(function() {
            $(this).addClass('warnning');
        });
        $('#err_msg').html('请输入手机号');
        return false;
    }

    
    if(error_num==0){
        return true;
    }else{
        return false;
    }
    
}
function add_sub_account(parent_mobile){

    var check_add=check_add_mobile(parent_mobile);

    var mobile_add=new Array();
    var mobile_i=0; 
    $("[name='mobile_add']").each(function(){
            var v=$(this).attr("value");
            if(trim(v)!=""){
                mobile_add[mobile_i]=v;
                mobile_i=mobile_i+1;
            }
            
    });
   
    
    if (!check_add) {return;};
    var ajaxurl = APP_ROOT+"/index.php?ctl=subuser&act=add_account";

    var query = new Object();    
    query.mobile =mobile_add;
    //alert(query.mobile);
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".accredit_confirm").attr('disabled', 'disabled');
                $("#error_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#error_msg").html('提交失败');
    　　　　}
                 $(".accredit_confirm").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {  
                window.location.href = APP_ROOT+"/subuser/manage_account";
            }
            else
            {
                if(ajaxobj.data.error==3){
                    $('#mobile_msg').html('手机号已存在');
                }else{
                    $('#mobile_msg').html(ajaxobj.info);
                }
               
            }
        },
        error:function(ajaxobj)
        {
            $('#err_msg').html('提交失败'); 
        }
    });
}

function update_sub_account_pwd(){
    
    if(get_input_value('user_pwd')==get_input_value('old_pwd')){
        $('#user_pwd_msg').html('密码未做修改');
        return;
    }
    var sub_account_json='[{"name":"user_pwd","password":true,"required":true,"error_id":"user_pwd_msg","error_msg":"请保证密码在6到12位且只包含数字和字母"}]';
    var res=csdk_check(sub_account_json);
    if (!res) {return};
    var ajaxurl = APP_ROOT+"/index.php?ctl=subuser&act=update_account_pwd";

    var query = new Object();    
    query.sub_user_pwd =get_input_value('user_pwd');
      
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".update_btn").attr('disabled', 'disabled');
                $("#error_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#error_msg").html('提交失败');
    　　　　}
                 $(".update_btn").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {   
                $("#user_pwd").val(query.sub_user_pwd);
                $("#old_pwd").val(query.sub_user_pwd);
                $('#err_msg').html('');
                $('#user_pwd_msg').html('');
                alert('修改成功'); 
            }
            else
            {
                $('#user_pwd_msg').html(ajaxobj.info);
            }
        },
        error:function(ajaxobj)
        {
            $('#err_msg').html('提交失败'); 
        }
    });

}
function show_notice(parent_mobile){
    var sub_account_json='[{"name":"user_pwd","password":true,"required":true,"error_id":"user_pwd_msg","error_msg":"请保证密码在6到12位且只包含数字和字母"}]';
    var res=csdk_check(sub_account_json);

    var phone_check=checkPhoneNum(parent_mobile);
     if (!phone_check || !res ) {return;}
    $("#show_mobile").empty();
    $("#show_mobile").html(phone_check);
    var mobile_str="";
    $(".point_out").show();
    $(".gray_box").show();
}
function sub_account_reset(){
    $(".point_out").hide();
    $(".gray_box").hide();
}
function continue_add(){
    window.location.href = APP_ROOT+"/subuser/add_account_show";
}
function my_account(){
    window.location.href = APP_ROOT+"/subuser/manage_account";
}
function del_show_notice(mobile_id){
    
    var mobile_num=$("#"+mobile_id).val();
    $("#show_mobile").empty();
    $(".mobile_blue").html(mobile_num);
    $("#del_mobile_num").val(mobile_id);;
    
    $(".point_out").show();
    $(".gray_box").show();
}
