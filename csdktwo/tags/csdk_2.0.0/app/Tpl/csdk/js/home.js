function checkImg(){
    if(get_input_value('img_user_logo')==''){
        $('#img_user_logo_msg').removeClass('gray');
        $('#img_user_logo_msg').html('请添加头像');
        $('.img_user_logo label').addClass('logored');
        $('.estp_logo label').addClass('logored');
        $('#user_img').focus();
        return false;
    }else{
        $('#img_user_logo_msg').html('点击图片重新上传');
        $('.img_user_logo label').removeClass('logored');
        $('.estp_logo label').removeClass('logored');
        return true;
    }

}
//在线网址
function checkOrgUrl(){
    var patt=/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\%&=]*)?$/i;
    //var deal_url=trim(document.getElementById(id).value);
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
function checkRegion(){
    if(trim(get_input_value('province'))==''){
        $('#region_msg').html('必填');
        $('#b_province').addClass('warnning');
        $('#b_city').addClass('warnning');
        $('#province').focus();
        return false;
    }else{
         
        $('#region_msg').html('');
        $('#b_province').removeClass('warnning');
        $('#b_city').removeClass('warnning');
        return true;
    }
}
function personal_msg_next(user_type){
  
    if (user_type==1) { 
        var personal_msg_json='[{"name":"per_history","max_len":100,"error_id":"per_history_msg","error_msg":"请确保内容在100个字以内"},\
                            {"name":"edu_history","max_len":100,"error_id":"edu_history_msg","error_msg":"请确保内容在100个字以内"},\
                      {"name":"per_brief","max_len":28,"required":true,"error_id":"per_brief_msg","error_msg":"请确保内容在28个字以内"},\
                      {"name":"per_sign","max_len":34,"required":true,"error_id":"per_desc_msg","error_msg":"请确保内容在34个字以内"},\
                      {"name":"user_name","max_len":14,"required":true,"error_id":"ex_user_msg","error_msg":"请确保内容在14个字以内"}]';
    }else{
        //var card_check=checkCardImg();
        var personal_msg_json='[{"name":"per_history","max_len":100,"error_id":"estp_per_history_msg","error_msg":"请确保内容在100个字以内"},\
                            {"name":"edu_history","max_len":100,"error_id":"estp_edu_history_msg","error_msg":"请确保内容在100个字以内"},\
                      {"name":"per_brief","max_len":28,"required":true,"error_id":"per_brief_msg","error_msg":"请确保内容在28个字以内"},\
                      {"name":"user_name","max_len":14,"required":true,"error_id":"ex_user_msg","error_msg":"请确保内容在14个字以内"}]';
    }
    var res=csdk_check(personal_msg_json);
    var region_check=checkRegion();
    var img_check=checkImg();
    if (user_type==1) { 
        if (!img_check || !region_check || !res) {return;}
         }else{
        if (!img_check  || !region_check || !res) {return;}
     }
    var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=personal_update";

    var query = new Object();    
    query.img_user_logo =get_input_value('img_user_logo');
    query.img_card_logo =get_input_value('img_card_logo');
    query.user_name =get_input_value('user_name');
    query.per_sign =get_input_value('per_sign');
    query.per_brief =get_input_value('per_brief');
    query.province =get_input_value('province');

    query.edu_history =get_input_value('edu_history');
    query.per_history =get_input_value('per_history');

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
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#error_msg").html('提交失败');
    　　　　}
                 $(".next").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {  
                if (user_type==1) {
                    window.location.href = APP_ROOT+"/home/investor_org";
                }else{
                    window.location.href = APP_ROOT+"/deal/deal_show";
                }
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


function checkCardImg(){
    if(get_input_value('img_card_logo')==''){
        $('#img_card_logo_msg').html('请上传您的个人名片照片');
        $('#card_src').addClass('warnning');
        $('.card_logo label').addClass('logored');
        $('#card_img').focus();
        return false;
    }else{
        $('#upload_pic').html('点击图片重新上传');
        $('.card_logo label').removeClass('logored');
        return true;
    }

}
function checkOrgLogo(){
    if(get_input_value('img_org_logo')==''){
        $('#img_org_logo_msg').html('请添加logo');
        $('#logo_img_src').addClass('warnning');
        $('.user_logo label').addClass('logored');
        $('#logo_img').focus();
        return false;
    }else{
        $('#img_org_logo_msg').html('点击图片重新上传');
        $('.user_logo label').removeClass('logored');
        return true;
    }

}
function organization_next(){  
    
    var card_check=checkCardImg();
   // var logo_check=checkOrgLogo();
    var url_check=checkOrgUrl();
    var organization_json='[{"name":"org_mobile","tel":true,"required":true,"error_id":"org_mobile_msg","error_msg":"联系方式不正确"},\
                            {"name":"org_linkman","max_len":14,"required":true,"error_id":"org_linkman_msg","error_msg":"请确保内容在14个字以内"},\
                      {"name":"org_desc","max_len":100,"required":true,"error_id":"org_brief_msg","error_msg":"请确保内容在100个字以内"},\
                      {"name":"org_name","max_len":14,"required":true,"error_id":"organization_msg","error_msg":"请确保内容在14个字以内"}]';
    var res=csdk_check(organization_json);
    if (!card_check || !url_check || !res ) {return;}
    var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=investor_org_update";

    var query = new Object();    
    query.img_org_logo =get_input_value('img_org_logo');
    query.img_card_logo =get_input_value('img_card_logo');
    query.org_name =get_input_value('org_name');
    query.org_desc =get_input_value('org_desc');
    query.org_mobile =get_input_value('org_mobile');
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
                $("#error_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#error_msg").html('提交失败');
    　　　　}
                 $(".next").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {   
                window.location.href = APP_ROOT+"/home/invest_about";
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
function checkPeriod(){
    var periods_check=0;
    $("[name='periods_names']").each(function(){
    if($(this).attr("class")=='blue'){
        periods_check+=1;
    }
    });
    if (periods_check==0) {
        $('#period_msg').html('必选');
        $("#cates").addClass('warnning');
        $('#cates').focus();
        return false;
    }else{
        $('#period_msg').html('');
        return true;
    }
}
function checkCate(){
    var cate_check=0;
    $("[name='cates_names']").each(function(){
    if($(this).attr("class")=='blue'){
        cate_check=cate_check+1;
    }
    });
    if (cate_check==0) {
        $('#cate_msg').html('必选');
        $("#cates").addClass('warnning');
        $('#cates').focus();
        return false;
    }else{
        $('#cate_msg').html('');
        return true;
    }
}

function invest_about_next(){
    var invest_about_json='[{"name":"mark_info_tag","box":1,"required":true,"error_id":"mark_msg",\
                            "input":[{"name":"mark_info","required":true,"max_len":14,"error_msg":"请确保内容在14个字以内"}]},\
                            {"name":"style_info_tag","box":1,"required":true,"error_id":"style_msg",\
                            "input":[{"name":"style_info","required":true,"max_len":14,"error_msg":"请确保内容在14个字以内"}]}]';
    var res=csdk_check(invest_about_json);
    var cate_check=checkCate();
    var period_check=checkPeriod();
    if (!period_check || !cate_check || !res) {return;}

    var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=invest_about_update";

    var query = new Object();    
    
    var mark_info=new Array();
    var mark_i=0; 
    $("[name='mark_info']").each(function(){
        if($(this).attr("value")!=$(this).attr("title")){
            mark_info[mark_i]=$(this).attr("value");
            mark_i=mark_i+1;
        }
    });
    query.mark_info =mark_info;

    var style_info=new Array();
    var style_i=0; 
    $("[name='style_info']").each(function(){
        if($(this).attr("value")!=$(this).attr("title")){
            style_info[style_i]=$(this).attr("value");
            style_i=style_i+1;
        }
    });
    query.style_info =style_info;
        
    var cates=new Array();
    var cate_i=0; 
    $("[name='cates_names']").each(function(){
        if($(this).attr("class")=='blue'){
            cates[cate_i]=$(this).attr("value");
            cate_i=cate_i+1;
        }
    });
    query.user_select_cate =cates;

    var periods=new Array(); 
    var period_i=0;
    $("[name='periods_names']").each(function(){
        if($(this).attr("class")=='blue'){
            periods[period_i]=$(this).attr("value");
            period_i=period_i+1;
        }
    });
    query.user_select_period =periods;
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
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#error_msg").html('提交失败');
    　　　　}
                 $(".next").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {  
               window.location.href = APP_ROOT+"/account/vip_account_introduction";
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




 