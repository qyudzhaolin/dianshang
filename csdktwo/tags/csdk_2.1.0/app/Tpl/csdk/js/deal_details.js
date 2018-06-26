//选择地区
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


//选择方向
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
        return false;
    }else{
        $('#cate_msg').html('');
        return true;
    }
}
//商业企划书
function checkBPImg(){
    if(trim(get_input_value('bp_url'))==''){
        $('#bp_url_msg').html('请上传商业企划书');
        $('#bp_real_url').addClass('warnning');
        $('#upload_plan label').addClass('logored');
        $('#bp').focus();
        return false;
    }else{
        $('#bp_url_msg').html('');
        $('#upload_plan label').removeClass('logored');
        return true;
    }
}
//选择阶段
function checkPeriod(){
    var period_check=0;
    $("[name='periods_names']").each(function(){
    if($(this).attr("class")=='blue'){
        period_check=period_check+1;
    }
    });
    if (period_check==0) {
        $('#period_msg').html('必选');
        $("#periods").addClass('warnning');
        return false;
    }else{
        $('#period_msg').html('');
        return true;
    }
}
//上传头像
function checkImg(){
    if(trim(get_input_value('img_deal_logo'))==''){
        $('#img_deal_logo_msg').html('请添加项目LOGO');
        $('#img_src').addClass('warnning');
        $('.estp_logo label').addClass('logored');
        $('#user_img').focus();
        return false;
    }else{
        $('#img_deal_logo_msg').html('点击图片重新上传');
         $('.estp_logo label').removeClass('logored');
        return true;
    }

}
//在线网址
function checkUrl(){
    var patt=/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\%&=]*)?$/i;
    //var deal_url=trim(document.getElementById(id).value);
    var deal_url=trim(get_input_value('deal_url'));
    if(trim(get_input_value('deal_url'))==''){
        $('#deal_url_msg').html('必填');
        $('#deal_url').addClass('warnning');
        $('#deal_url').focus();
        //$('#deal_url').focus();
        return false;
    }else{
        if(patt.test(deal_url)){
            $('#deal_url_msg').html('');
            return true;
        }else{
            $('#deal_url_msg').html('在线网址格式不正确');
            $('#deal_url').addClass('warnning');
            $('#deal_url').focus();
            return false;          
        }

    }

}
/*悬浮条*/
$(function() {
    $(window).scroll(function(event) {
        var num=$(window).scrollTop();
        if(num>0){
            $('.header').css({'opacity':0});
            $('.ss').show();
        }else{
            $('.ss').hide();
            $('.header').css({'opacity':1});
        }
    });
});

function deal_next(){
   
    var region_check=checkRegion();  
    var cate_check=checkCate();
    var bpimg_check=checkBPImg();
    var period_check=checkPeriod();
    var img_check=checkImg();
    var url_check=checkUrl();
    var check_json='[\
      {"name":"point_info_tag","box":1,"required":true,"error_id":"point_msg",\
        "input":[{"name":"point_info","required":true,"error_msg":"必填"}]},\
      {"name":"create_time_event_tag","box":1,"required":true,"error_id":"create_time_msg",\
        "input":[{"name":"create_time_event","required":true,"error_msg":"请选择日期"},\
                 {"name":"brief_event","required":true,"max_len":14,"error_msg":"请确保内容在14个字以内"}]},\
     {"name":"name","required":true,"max_len":14,"error_id":"name_msg","error_msg":"请确保内容在14个字以内"},\
     {"name":"deal_sign","required":true,"max_len":34,"error_id":"deal_sign_msg","error_msg":"请确保内容在34个字以内"},\
     {"name":"deal_brief","required":true,"max_len":200,"error_id":"deal_brief_msg","error_msg":"请确保内容在200个字以内"},\
     {"name":"business_mode","required":true,"max_len":100,"error_id":"business_mode_msg","error_msg":"请确保内容在100个字以内"}\
      ]';

    var json_ok=csdk_check(check_json);
    
    if(!region_check || !cate_check || !bpimg_check || !period_check || !img_check || !url_check || !json_ok){
        return;
    }
    var ajaxurl = APP_ROOT+"/index.php?ctl=deal&act=deal_org_msg";

    var query = new Object();
    query.img_deal_logo =get_input_value('img_deal_logo');    
    query.dealname =get_input_value('name');
    query.deal_sign =get_input_value('deal_sign');
    query.province =get_input_value('province');
    query.city =get_input_value('city');
    query.deal_url =get_input_value('deal_url');
    query.deal_brief =get_input_value('deal_brief');
    query.business_mode =get_input_value('business_mode');
    query.bp_url =get_input_value('bp_url');
   
    var periods_names=new Array();
    var i=0;
    $("[name='periods_names']").each(function(index){
        if($(this).attr("class")=='blue'){
            periods_names[i]=$(this).attr("value");
            i+=i;
        }
    });
    query.user_select_periods =periods_names;

    var cates_names=new Array();
    var i=0;
    $("[name='cates_names']").each(function(iidex){
        if($(this).attr("class")=='blue'){
            cates_names[i]=$(this).attr("value");
            i++;
        }
    });
    query.deal_select_cate =cates_names;

    var point_info=new Array();
    $("[name='point_info']").each(function(index){
        point_info[index]=$(this).attr("value");
    });
    query.point_info =point_info;

    var create_time_event=new Array();
    $("[name='create_time_event']").each(function(index){
        create_time_event[index]=$(this).attr("value");
    });
    query.create_time_event =create_time_event;
    var brief_event=new Array();
    $("[name='brief_event']").each(function(index){
        brief_event[index]=$(this).attr("value");
    });
    query.brief_event =brief_event;
    
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
                window.location.href = APP_ROOT+"/deal/estp_finacing";
            }
            else
            {
                $("#error_msg").html(ajaxobj.info); 
            }
        },

        error:function(ajaxobj)
        {
            $("#error_msg").html('提交失败');
        }
    });

}

function company_next(){
   
    var company_next_json='[{"name":"company_name","max_len":14,"required":true,"error_id":"company_name_msg","error_msg":"请确保内容在14个字以内"},\
                            {"name":"company_brief","max_len":100 ,"required":true,"error_id":"company_brief_msg","error_msg":"请确保内容在100个字以内"},\
                            {"name":"company_title","max_len":28,"required":true,"error_id":"company_title_msg","error_msg":"请确保内容在14个字以内"},\
                            {"name":"company_scope","max_len":28,"required":true,"error_id":"company_title_msg","error_msg":"请确保内容在28个字以内"},\
                            {"name":"deal_team_tag","box":1,"required":true,"error_id":"team_msg",\
                                "input":[{"name":"team_name","required":true,"error_msg":"请确保内容在14个字以内"},\
                                        {"name":"team_title","required":true,"error_msg":"请确保内容在14个字以内"},\
                                        {"name":"team_intro","required":true,"error_msg":"请确保内容在28个字以内"}]}]';
    var res=csdk_check(company_next_json);
    if (!res) {return};
    var ajaxurl = APP_ROOT+"/index.php?ctl=deal&act=company_org_msg";

    var query = new Object();    
    query.company_name =get_input_value('company_name');
    query.company_brief =get_input_value('company_brief');
    query.company_title =get_input_value('company_title');
    query.company_scope =get_input_value('company_scope');
    var team_name=new Array();
    var name_i=0; 
    $("[name='team_name']").each(function(){
        if($(this).attr("value")!=$(this).attr("title")){
            team_name[name_i]=$(this).attr("value");
            name_i=name_i+1;
        }
    });
    query.team_name =team_name;
    var team_title=new Array(); 
    var title_i=0;
    $("[name='team_title']").each(function(){
        if($(this).attr("value")!=$(this).attr("title")){
            team_title[title_i]=$(this).attr("value");
            title_i=title_i+1;
        }
    });
    query.team_title =team_title;

    var team_intro=new Array(); 
    var intro_i=0;
    $("[name='team_intro']").each(function(){
        if($(this).attr("value")!=$(this).attr("title")){
            team_intro[intro_i]=$(this).attr("value");
            intro_i=intro_i+1;
        }
    });
    query.team_intro =team_intro;
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
                window.location.href = APP_ROOT+"/auth/estp_show";
            }
            else
            {
                $("#error_msg").html(ajaxobj.info); 
            }
        },
        error:function(ajaxobj)
        {       
             $("#error_msg").html('提交失败');
        }
    });

}


function preview_next(){
   
    var company_next_json='[{"name":"company_name","max_len":14,"required":true,"error_id":"company_name_msg","error_msg":"请确保内容在14个字以内"},\
                            {"name":"company_brief","max_len":100 ,"required":true,"error_id":"company_brief_msg","error_msg":"请确保内容在100个字以内"},\
                            {"name":"company_title","max_len":28,"required":true,"error_id":"company_title_msg","error_msg":"请确保内容在14个字以内"},\
                            {"name":"company_scope","max_len":28,"required":true,"error_id":"company_title_msg","error_msg":"请确保内容在28个字以内"},\
                            {"name":"deal_team_tag","box":1,"required":true,"error_id":"team_msg",\
                                "input":[{"name":"team_name","required":true,"error_msg":"请确保内容在14个字以内"},\
                                        {"name":"team_title","required":true,"error_msg":"请确保内容在14个字以内"},\
                                        {"name":"team_intro","required":true,"error_msg":"请确保内容在28个字以内"}]}]';
    var res=csdk_check(company_next_json);
    if (!res) {return};
    var ajaxurl = APP_ROOT+"/index.php?ctl=deal&act=preview_org_msg";

    var query = new Object();    
    query.company_name =get_input_value('company_name');
    query.company_brief =get_input_value('company_brief');
    query.company_title =get_input_value('company_title');
    query.company_scope =get_input_value('company_scope');
    query.preview_id =get_input_value('preview_id');
    var team_name=new Array();
    var name_i=0; 
    $("[name='team_name']").each(function(){
        if($(this).attr("value")!=$(this).attr("title")){
            team_name[name_i]=$(this).attr("value");
            name_i=name_i+1;
        }
    });
    query.team_name =team_name;
    var team_title=new Array(); 
    var title_i=0;
    $("[name='team_title']").each(function(){
        if($(this).attr("value")!=$(this).attr("title")){
            team_title[title_i]=$(this).attr("value");
            title_i=title_i+1;
        }
    });
    query.team_title =team_title;

    var team_intro=new Array(); 
    var intro_i=0;
    $("[name='team_intro']").each(function(){
        if($(this).attr("value")!=$(this).attr("title")){
            team_intro[intro_i]=$(this).attr("value");
            intro_i=intro_i+1;
        }
    });
    query.team_intro =team_intro;

    var previewurl=0;
    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        async:false,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".next").attr('disabled', 'disabled');
                //$("#error_msg").html('提交中...');
                $("#error_msg").html('');
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
               // var newTab=window.open('about:blank');
                $(".next").removeAttr('disabled');
                $("#error_msg").html('');
                previewurl=1;
                
            }
            else
            {
                $("#error_msg").html(ajaxobj.info); 
            }
        },
        error:function(ajaxobj)
        {       
             $("#error_msg").html('提交失败');
        }
    });
    if(previewurl==1){
    window.open(APP_ROOT+"/dealpreview/"+query.preview_id+"/",'_blank');
    }
}


/****Author: Zhu, HuanJun***********/
/****Date: 2015/7/15******/
/****Functionality: 决定关注BUTTON的点击效果， 并且与DB 交互***/

function add_focus()
{
    var status=true;
    if(status){
        $('.attention').html('已关注该项目');
        $('.attention').css({'color':'#666666','cursor':'auto'});
        $('#atten').addClass('selected');
    }

    var execute_url = APP_ROOT+"/index.php?ctl=deal&act=add_focus";
    var project_id =$('#project_id').val();
    //alert(project_id);
    //exit;
    //console.log(project_id);

    var user_id =$('#user_id').val();
    
    var json_data ={
        'project_id':project_id
        ,'user_id': user_id
    };
    $.post(
        execute_url
        ,json_data
        ,function(data){
            console.log(data);//call_fun(data);
        }
        ,"json"
    );

}
/****Author: Zhu, HuanJun***********/
/****Date: 2015/7/15******/
/****Functionality: 决定投资相关BUTTON的点击效果， 并且与DB 交互***/
function send_request(){
    var ajaxurl = APP_ROOT+"/index.php?ctl=dealdetail&act=add_invest";

    var count_invest_id =$('#count_invest').val();
    
    var query = new Object(); 
    query.project_id =$('#project_id').val(); 

    var ajaxTimeoutTest=$.ajax({ 
        url: ajaxurl,
        timeout:20000,
        dataType: "json",
        data:query,
        type: "POST",
        beforeSend: function () {
                $(".send_request_msg").attr('disabled', 'disabled');
                $("#error_msg").html('提交中...');
            },
        complete: function (XMLHttpRequest,status) {
            if(status=='timeout'){//超时,status还有success,error等值的情况
     　　　　　 ajaxTimeoutTest.abort();
    　　　　　  $("#error_msg").html('提交失败');
    　　　　}
                 $(".send_request_msg").removeAttr('disabled');
            },
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {   
                $('.gray_box').show();
                $('.winbox').show();
                $('.send_request_msg').show();
                $('.send_request').hide();
                $('#count_investnum').html(parseInt(count_invest_id)+1);
            }
            else
            {
                //alert(ajaxobj.info);
            }
        },
        error:function(ajaxobj)
        {
            $("#error_msg").html('提交失败');
        }
    });

    
    
}
function close_win(){
    $('.winbox').hide();
    $('.gray_box').hide();
}
/****Author: Zhu***********/
/****Date: 2015/7/14******/
/****Functionality: 进入项目详情时，决定投资相关BUTTON 和 关注BUTTON 的显示***/
$(document).ready(function() {
    var project_follow = $('#project_follow').val();
    var project_invest = $('#project_invest').val();
    console.log(project_follow);
    if(project_follow)
    {
        $('.attention').html('已关注该项目');
        $('.attention').css({'color':'#666666','cursor':'auto'});
        $('#atten').addClass('selected');
    }
    if(project_invest)
    {
        $('.send_request_msg').show();
        $('.send_request').hide();
        $('#count_invest').html('')
    }
    
}) ;
// 融资后估计计算
function pe_evaluateData(){
    var pe_amount_plan=trim(get_input_value('pe_amount_plan'));
    var pe_sell_scale=trim(get_input_value('pe_sell_scale'));
    var pe_evaluate=parseInt(pe_amount_plan/pe_sell_scale*100);
     if(pe_evaluate&&pe_amount_plan&&pe_sell_scale)
     {
         $('#pe_evaluate').val(pe_evaluate);
     }
}
function estp_finacing(){
    
    var check_json='[\
      {"name":"create_time_event_tag","box":1,"required":false,"error_id":"invest_history_msg",\
        "input":[{"name":"create_time_event","required":false,"error_msg":"请选择日期"},\
                 {"name":"deal_trade_brief","required":false,"max_len":14,"error_msg":"请在14个字以内填写机构名称"},\
                 {"name":"deal_trade_period","required":false,"max_len":5,"error_msg":"请在5个字以内填写融资轮次"},\
                 {"name":"invest_price","required":false,"number":true,"error_msg":"必须填写数字"}]\
      },\
     {"name":"pe_amount_plan","min_num":0,"required":true,"number":true,"error_id":"pe_amount_plan_msg","error_msg":"必须填写数字"},\
     {"name":"pe_sell_scale","max_len":2,"min_num":0,"required":true,"number":true,"error_id":"pe_sell_scale_msg","error_msg":"必须填写数字"},\
     {"name":"pe_least_amount","min_num":99,"required":true,"number":true,"error_id":"pe_least_amount_msg","error_msg":"必须填写数字"},\
    ]';
   
    if(!csdk_check(check_json)){
        return;
    }   
   
    var ajaxurl = APP_ROOT+"/index.php?ctl=deal&act=add_estp_finacing";

    var query = new Object(); 

    query.pe_amount_plan = get_input_value('pe_amount_plan');  
    query.pe_sell_scale = get_input_value('pe_sell_scale');  
    query.pe_least_amount = get_input_value('pe_least_amount');  


    var create_time_event=new Array();
    $("[name='create_time_event']").each(function(index){
        create_time_event[index]=$(this).attr("value");
        if (create_time_event[index]==$(this).attr("title")) {
            create_time_event[index]="";
        };
    });
    query.create_time_event =create_time_event;

    var deal_trade_period=new Array();
    $("[name='deal_trade_period']").each(function(index){
        deal_trade_period[index]=$(this).attr("value");
        if (deal_trade_period[index]==$(this).attr("title")) {
            deal_trade_period[index]="";
        };
    });
    query.deal_trade_period = deal_trade_period;

    var deal_trade_brief=new Array();
    $("[name='deal_trade_brief']").each(function(index){
        deal_trade_brief[index]=$(this).attr("value");
        if (deal_trade_brief[index]==$(this).attr("title")) {
            deal_trade_brief[index]="";
        };
    });
    query.deal_trade_brief = deal_trade_brief;

    var invest_price=new Array();
    $("[name='invest_price']").each(function(index){
        invest_price[index]=$(this).attr("value");
        if (invest_price[index]==$(this).attr("title")) {
            invest_price[index]="";
        };
        
    });
    query.invest_price = invest_price;

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
                window.location.href = APP_ROOT+"/deal/company_org";
            }
            else
            {
                $("#error_msg").html(ajaxobj.info); 
            }
        },
        error:function(ajaxobj)
        {
            $("#error_msg").html('提交失败');
        }
    });

}

$(function(){
        $('.attention_item li:odd').addClass('no_margin');
        $('.attention_item_box:odd').addClass('no_margin');
        $('#my_attention_item:odd').addClass('no_margin');
        $('.memb_list:odd').addClass('no_margin');
        var status=true;
        $('.filtrate_box').click(function(){
            if(status){
                $('.deals_box').show();
                $('.filtrate').hide();
                status=false;
            }
        });
        $('.react_top').click(function(){
            $('.deals_box').hide();
            $('.filtrate').show();
            status=true;
        });
    
});

$(document).ready(function() {
    if($("#deal_detail").val()!=undefined){
        window.addEventListener("scroll",function(){
               change_class("business","business_class");
               change_class("model","model_class");
               change_class("invest","invest_class");
               change_class("team","team_class");
               change_class("point","point_class");
               change_class("item","item_class");
         });
    }
});

function change_class(pos_id,bar_id){
     var view_top =  $(window).scrollTop(); //可见高度顶部
     var view_bottom =  view_top+$(window).height(); //可见高度底部
     var top=$('#'+pos_id).offset().top;//alert(top);
     
     if(top>=view_top&&view_bottom>=top){
        $(".tt").removeClass('tt');
        $("#"+bar_id).addClass('tt');
     }
}

 
 