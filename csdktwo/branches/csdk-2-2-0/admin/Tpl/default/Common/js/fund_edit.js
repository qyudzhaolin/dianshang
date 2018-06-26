function add_team()
{
	var len=$("input[name=image_key[]]").size();
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"=Deal&"+VAR_ACTION+"=add_team&len="+len, 
		data: "ajax=1",
		success: function(obj){
			$("#faq").append(obj);
		}
		
		});
}
 
function check_fund_field(field_id,is_require,max_len,img_file_id){
	 var field=$("[name="+field_id+"]").val();
	 field=trim(field);
	 console.log(field);
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

function rate_check(){
	var a = parseFloat($("#fund_income_rate_director").val()) * 100;
	var b = parseFloat($("#fund_income_rate_invester").val()) * 100;
	var c = parseFloat($("#fund_income_rate_cisdaq").val()) * 100;
	var d = parseFloat($("#fund_income_rate_partner").val()) * 100;
//	if((a || b || c || d) && (a + b + c + d != 10000)){
	if(a + b + c + d != 10000){
		$("#fund_income_rate_invester_msg").html('四项分配占比之和须等于100%');
        $("#fund_income_rate input").addClass('warnning');
        $("#fund_income_rate input").focus();
		return false;
	}else{
		return true;
	}
}
 
function add_fund_check(add_or_edit){
	//{"name":"code","required":true,"max_len":10,"error_id":"code_msg","error_msg":"基金编码不超10位"},\
	// {"name":"max_payback","required":true,"number":true,"error_id":"max_payback_msg","error_msg":"必须填数字"},\
	// {"name":"average_payback","required":true,"number":true,"error_id":"average_payback_msg","error_msg":"必须填数字"},\
	// {"name":"total_payback","required":true,"number":true,"error_id":"total_payback_msg","error_msg":"必须填数字"},\
	var check_json='[\
        {"name":"name","required":true,"max_len":30,"error_id":"name_msg","error_msg":"基金名称不超30位"},\
        {"name":"short_name","required":true,"max_len":6,"error_id":"short_name_msg","error_msg":"基金短名称不超6个字"},\
        {"name":"total_amount","min_num":0,"required":true,"number":true,"error_id":"total_amount_msg","error_msg":"必须填数字"},\
        {"name":"establish_date","required":true,"error_id":"establish_date_msg"},\
        {"name":"deadline","required":true,"error_id":"deadline_msg"},\
        {"name":"summary","required":true,"max_len":200,"error_id":"summary_msg","error_msg":"基金简介不超200位"},\
		{"name":"fund_income_rate_director","required":true,"error_id":"fund_income_rate_director_msg"},\
		{"name":"fund_income_rate_invester","required":true,"error_id":"fund_income_rate_invester_msg"},\
		{"name":"fund_income_rate_cisdaq","required":true,"error_id":"fund_income_rate_cisdaq_msg"},\
		{"name":"fund_income_rate_partner","required":true,"error_id":"fund_income_rate_partner_msg"},\
        {"name":"add_team","box":1,"required":true,"error_id":"team_msg",\
            "input":[{"name":"position[]","required":true,"max_len":8,"error_msg":"请确保职位在8个字以内"},\
                    {"name":"brief[]","required":true,"max_len":200,"error_msg":"请确保内容在200个字以内"}]},\
        ]';
	var check_all=csdk_check(check_json);
	var managers_id_check=check_fund_field("managers_id",1,0,"");
	var fund_rate_check=rate_check();
	
	if(!check_all || !managers_id_check || !fund_rate_check){
		return false;
	}else{
		return true;
	}
}


function add_fund_investor_check(add_or_edit){
	var check_json='[\
		{"name":"user_name","required":true,"error_id":"user_name_msg"},\
		{"name":"user_id","required":true,"error_id":"user_id_msg"},\
		{"name":"user_mobile","required":true,"error_id":"user_mobile_msg"},\
		{"name":"investor_amount","required":true, "number":true,"error_id":"investor_amount_msg","error_msg":"必须填数字"},\
		{"name":"investor_rate","required":true,"number":true,"error_id":"investor_rate_msg","error_msg":"必须填数字"},\
		{"name":"create_time","required":true,"error_id":"create_time_msg"},\
    ]';

	var check_all = csdk_check(check_json);
	var partner_user_id_check = false;
	
	if($("#partner_user_id").val()){
		partner_user_id_check = true;
	}else{
		if($("#partner_user_id").next().find("input").attr("checked")){
			partner_user_id_check = true;
		}
	}
	if(!partner_user_id_check){
		console.log(partner_user_id_check);
		$("#partner_user_id_msg").html("必选");
	}
	if(!check_all || !partner_user_id_check){
		return false;
	}else{
		return true;
	}  
}

function fund_invest_deal_check(add_or_edit){
	if(add_or_edit == 'add'){
		var check_json='[\
			{"name":"deal_name","required":true,"error_id":"deal_name_msg"},\
			{"name":"deal_id","required":true,"error_id":"deal_id_msg"},\
			{"name":"investor_amount","required":true, "number":true,"error_id":"investor_amount_msg","error_msg":"必须填数字"},\
			{"name":"investor_date","required":true,"error_id":"investor_date_msg"},\
	    ]';
	} else{
		var check_json='[\
			{"name":"deal_name","required":true,"error_id":"deal_name_msg"},\
			{"name":"deal_id","required":true,"error_id":"deal_id_msg"},\
			{"name":"investor_amount","required":true, "number":true,"error_id":"investor_amount_msg","error_msg":"必须填数字"},\
			{"name":"investor_date","required":true,"error_id":"investor_date_msg"},\
	    ]';
	}

    var check_all=csdk_check(check_json);
	if(!check_all){
		return false;
	}else{
		return true;
	}  
}

// 添加投资人，是否有渠道合伙人
function partner_user_check(obj){
	if(obj.checked){
		$("#partner_user_id").val("");
	}
}

$(document).ready(function() {
 	$('input[type=text],input[type=password],textarea,select').live('blur',function(){
 		var v=$(this).val();
		if (v!=''){
			$(this).removeClass("warnning");
		}
	});
 	$("#partner_user_id").click(function(){
 		if($(this).val()){
 			$(this).next().find("input").attr("checked",false);
 		}
 	})
 	$("#investor_amount").blur(function(){
 		var amount = $(this).attr("data-amount");
 		$("#investor_rate").val(($(this).val() / amount * 100).toFixed(2));
 	})
 	$("#managers_id").hover(function(){
 		$("#managers_id_msg2").toggle();
 	});
 	
 	// 基金阶段的切换
 	$("#fund_period").change(function(){
 		// 清空信息
 		if($(this).attr('data-type') == "add"){
 			$("#csdq_online_period input,#csdq_raising_period input,#csdq_raising_period textarea").val("");
 			$("#csdq_online_period[name=error_msg],#csdq_raising_period[name=error_msg]").html("");
 		}
 		
 		// 显示区域
 		var fund_period = $(this).val();
 		if(fund_period == 1){
 			$("#csdq_online_period").hide();
 			$("#csdq_raising_period").show();
 		}else if(fund_period == 2){
 			$("#csdq_raising_period").hide();
 			$("#csdq_online_period").show();
 		}else{
 			$("#csdq_raising_period").hide();
 			$("#csdq_online_period").hide();
 		}
 	});
});