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
	 var field=trim($("[name="+field_id+"]").val());
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
	if(a + b + c + d != 10000){
		$("#fund_income_rate_invester_msg").html('四项分配占比之和须等于100%');
        $("#fund_income_rate input").addClass('warnning');
        $("#fund_income_rate input").focus();
		return false;
	}else{
		return true;
	}
}
 
function img_text_check(field_img,field_text,max_len){
	var img1 = $("#"+field_img+"_app_img").val();
	var img2 = $("#"+field_img+"_pc_img").val();
	var texts = $("#"+field_text).val();
	
	if(!img1 && !img2 && !texts){
		$("#"+field_text+"_msg").html('至少完善一处信息');
		$("#"+field_text).focus();
		return false;
	}else{
		if(texts && texts.length > max_len){
			$("#"+field_text+"_msg").html('请确保内容在'+max_len+'个字以内');
			$("#"+field_text).addClass('warnning');
			$("#"+field_text).focus();
            return false;
		}else{
			return true;
		}
	}

}

// 募集阶段的基金->设立方案的检测
function raising_check(){
	
	// 设立方案7项必填一项，图片若传，app+pc都必须传
	var check_arr = [
	                 ['advantage','advantage_app_img','advantage_pc_img'],
	                 ['invest_principle','invest_principle_app_img','invest_principle_pc_img'],
	                 ['decision_process','decision_process_app_img','decision_process_pc_img'],
	                 ['exit_channel','exit_channel_app_img','exit_channel_pc_img'],
	                 ['income_share','income_share_app_img','income_share_pc_img'],
	                 ['income_rate_calculate','income_rate_calculate_app_img','income_rate_calculate_pc_img'],
	                 ['risk_type','risk_type_app_img','risk_type_pc_img'],
	                 ];
	
	// 先判断是否全部都是空的
	var plan_all_check = true;
	
	$.each(check_arr,function(k,v){
		var tmp_val 		= $.trim($("#"+v[0]).val());
		var tmp_val_app 	= $.trim($("#"+v[1]).val());
		var tmp_val_pc 		= $.trim($("#"+v[2]).val());
		
		if(tmp_val || tmp_val_app || tmp_val_pc){
			plan_all_check = false;	// 有值
			return false;
		}
	})
	
	if(plan_all_check){
		// 全部都是空的
		console.log("alls empty");
		$("#risk_type_msg").html("设立方案相关7小项至少完善一处信息");
		return false;
		
	}else{
		var raising_pass = true;	// 是否通过检测
		
		// 存在值了，则针对每项去判断
		$.each(check_arr,function(k,v){
			
			var tmp_val 		= $.trim($("#"+v[0]).val());
			var tmp_val_app 	= $.trim($("#"+v[1]).val());
			var tmp_val_pc 		= $.trim($("#"+v[2]).val());
			
			if(tmp_val == "" && tmp_val_app == "" && tmp_val_pc == ""){
				var all_val = false;	// 都为空
			}else{
				var all_val = true;	// 有值
			}
			
			var plan_all = plan_img = true;
			
			if(all_val){
				// 存在值，需判断存在哪个字段，因为图片是都必传的
				if(tmp_val){
					// 如果存在文字
					if((tmp_val_app && tmp_val_pc == "") || (tmp_val_app == "" && tmp_val_pc)){
						// 只存在一张图片，不通过，提示图片都必传
						plan_img = false;
					}else{
						// 不存在图片或者都存在，通过
						
					}
					
				}else{
					// 如果存在图片
					if(tmp_val_app && tmp_val_pc){
						// 都存在，通过
						
					}else{
						// 不存在图片或者只存在一张图片，不通过，提示图片都必传
						plan_img = false;
					}
					
				}
				
			}else{
				// 全部为空，针对此项->不通过
				plan_all = false;
			}
			
			console.log("------- "+v[0]+" --------");
			console.log("textarea: "+tmp_val);
			console.log("app_img: "+tmp_val_app);
			console.log("pc_img: "+tmp_val_pc);
			
			if(plan_all){
				if(plan_img){
					console.log(" pass");
				}else{
					// 加提示，并终止循环
					console.log(" pc,app图片都不能为空");
					$("#"+v[0]+"_msg").html(" 若要上传pc,app图片，则都不能为空");
					$("#"+v[0]).focus();
					raising_pass = false;
					return false;
				}
			}else{
				console.log(" all empty");
			}
		});
		
		// 没问题，返回true
		if(raising_pass){
			return true;
		}else{
			return false;
		}
	}
}

function add_fund_check(add_or_edit){
	// 基础
	var check_json='[\
        {"name":"name","required":true,"max_len":30,"error_id":"name_msg","error_msg":"基金名称不超30位"},\
        {"name":"short_name","required":true,"max_len":6,"error_id":"short_name_msg","error_msg":"基金短名称不超6个字"},\
        {"name":"total_amount","min_num":10000,"max_num":9999999,"required":true,"number":true,"error_id":"total_amount_msg"},\
        {"name":"establish_date","required":true,"error_id":"establish_date_msg"},\
        {"name":"deadline","required":true,"error_id":"deadline_msg"},\
		{"name":"invest_min_amount","min_num":100,"required":true,"error_id":"invest_min_amount_msg"},\
        {"name":"summary","required":true,"max_len":200,"error_id":"summary_msg","error_msg":"基金简介不超200位"},\
        {"name":"add_team","box":1,"required":true,"error_id":"team_msg",\
            "input":[{"name":"position[]","required":true,"max_len":8,"error_msg":"请确保职位在8个字以内"},\
                    {"name":"brief[]","required":true,"max_len":200,"error_msg":"请确保内容在200个字以内"}]},\
        ]';
	
	var check_all = csdk_check(check_json);
	var managers_id_check = check_fund_field("managers_id",1,0,"");
	var fund_period_check = check_fund_field("fund_period",1,0,"");
	
	if(!check_all || !managers_id_check || !fund_period_check){
		return false;
	}
	
	var fund_period = $("#fund_period").val();
	if(fund_period == 1){
		
		// 募资
		var check_json3='[\
			{"name":"raising_start_date","required":true,"error_id":"raising_start_date_msg"},\
			{"name":"raising_end_date","required":true,"error_id":"raising_end_date_msg"},\
			{"name":"intend_profession","required":true,"max_len":200,"error_id":"intend_profession_msg","error_msg":"不超200字"},\
			{"name":"pace_of_invest","required":true,"max_len":200,"error_id":"pace_of_invest_msg","error_msg":"不超200字"},\
			{"name":"manage_fee","required":true,"max_len":200,"error_id":"manage_fee_msg","error_msg":"不超200字"},\
			{"name":"invest_type","required":true,"max_len":200,"error_id":"invest_type_msg","error_msg":"不超200字"},\
			{"name":"vote_type","required":true,"max_len":100,"error_id":"vote_type_msg","error_msg":"不超100字"},\
			{"name":"value_orientation","required":true,"max_len":200,"error_id":"value_orientation_msg","error_msg":"不超200字"},\
			]';
		
		var check_all3 = csdk_check(check_json3);
		var profession_c = img_text_check("profession","profession_info",400);
		var invest_philosophy_c = img_text_check("invest_philosophy","invest_philosophy",400);
		var raising_check_c = raising_check();
		if(!check_all3 || !profession_c || !invest_philosophy_c || !raising_check_c){
			return false;
		}
		
	}else if(fund_period == 2){
		
		// 管理
		var check_json2='[\
			{"name":"fund_income_rate_director","required":true,"error_id":"fund_income_rate_director_msg"},\
			{"name":"fund_income_rate_invester","required":true,"error_id":"fund_income_rate_invester_msg"},\
			{"name":"fund_income_rate_cisdaq","required":true,"error_id":"fund_income_rate_cisdaq_msg"},\
			{"name":"fund_income_rate_partner","required":true,"error_id":"fund_income_rate_partner_msg"},\
			]';
		
		var check_all2 = csdk_check(check_json2);
		var fund_rate_check = rate_check();
		
		if(!check_all2 || !fund_rate_check){
			return false;
		}
	} else {
		return true;
	}
	
}


function add_fund_investor_check(add_or_edit){
	// 起投金额
	var invest_min_amount 	= $("#f_invest_min_amount").val();
	
	var check_json='[\
		{"name":"user_name","required":true,"error_id":"user_name_msg"},\
		{"name":"user_id","required":true,"error_id":"user_id_msg"},\
		{"name":"user_mobile","required":true,"error_id":"user_mobile_msg"},\
		{"name":"investor_amount","required":true,"min_num":'+invest_min_amount+',"number":true,"error_id":"investor_amount_msg","error_msg":"必须填数字"},\
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
 	$('input,textarea,select').live('blur',function(){
 		var v=$(this).val();
		if (v!=''){
			$(this).removeClass("warnning");
			$(this).siblings("[name=error_msg]").html("");
		}
	});
 	$("#partner_user_id").click(function(){
 		if($(this).val()){
 			$(this).next().find("input").attr("checked",false);
 		}
 	})
 	$("#investor_amount").blur(function(){
 		var total_amount 		= parseInt($("#f_total_amount").val());
 		var invest_min_amount 	= parseInt($("#f_invest_min_amount").val());
 		var investor_amount 	= parseInt($(this).val());
 		
 		if(investor_amount < invest_min_amount){
 			$("#investor_amount_msg").html("购买金额需大于起投金额["+invest_min_amount+"万元]");
 			$("#investor_amount").focus();
 		}else{
 			$("#investor_amount_msg").html("");
 			$("#investor_rate").val((investor_amount / total_amount * 100).toFixed(2));
 		}
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
 	$("#fund_period").trigger("change");
	
});