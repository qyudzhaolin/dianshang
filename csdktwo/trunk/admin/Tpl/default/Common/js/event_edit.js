function add_event_history_check(obj,val){
	val = $.trim(val);
	if(val == ''){
		obj.addClass("warnning");
		obj.next().html("必填");
		return false;
	}
	
	return true;
}

function add_event_check(){
	var check_json_1 = '[\
		{"name":"investor_record_type","required":true,"number":true,"error_id":"investor_record_type_msg","error_msg":"必选"},\
		{"name":"period","required":true,"error_id":"period_msg","error_msg":"必选"},\
		{"name":"pe_amount_plan","required":true,"error_id":"pe_amount_plan_msg","error_msg":"必填"},\
		{"name":"pe_sell_scale","required":true,"error_id":"pe_sell_scale_msg","error_msg":"必填"},\
		{"name":"investor_before_evalute","required":true,"error_id":"investor_before_evalute_msg","error_msg":"必填"},\
		]';
	// 预期融资信息
	var investor_record_type = $("#investor_record_type").val();
	if(investor_record_type == 1 && $("#investor_record_type").attr("data-type") == 1){
		$("#investor_record_type").addClass("warnning");
		$("#investor_record_type").next().html('已有最新轮次');
		return false;
	}
	if (!csdk_check(check_json_1)) {
		return false;
	}
	
	if(investor_record_type == 2){
		// 实际融资信息
		
		
		// 投资基金
		var isSubmit = edit_event_check();
		
		if(isSubmit){
			return true;
		}else{
			return false;
		}
	}
	return true;
	
}

function edit_event_check(){
	// 投资基金
	var isSubmit = false;
	var nameArr = fundArr = new Array(); 
	$.each($("#csdq_hisory_area .csdq_funds"),function(k,v){
		isSubmit = false;
		
		var is_csdk_fund = $(this).find("select:eq(0)").val();
		var investor_date = $(this).find(".investor_date").val();
		if(!add_event_history_check($(this).find(".investor_date"),investor_date)){
			$(this).find(".investor_date").focus();
			return false;
		}
		// 是否磁斯达克参与
		if(is_csdk_fund == 2){
			var name = $.trim($(this).find(".name").val());
			if($.inArray(name, nameArr) > -1){
				$(this).find(".name").addClass("warnning");
				$(this).find(".name").next().html("基金重复");
				$(this).find(".name").focus();
				return false;
			}
			nameArr[k] = name;
			if(!add_event_history_check($(this).find(".name"),name)){
				$(this).find(".name").focus();
				return false;
			}
		}else{
			var fund_id = $.trim($(this).find("select:eq(1)").val());
			if($.inArray(fund_id, fundArr) > -1){
				$(this).find("select:eq(1)").addClass("warnning");
				$(this).find("select:eq(1)").next().html("基金重复");
				$(this).find("select:eq(1)").focus();
				return false;
			}
			fundArr[k] = fund_id;
			if(!add_event_history_check($(this).find("select:eq(1)"),fund_id)){
				$(this).find("select:eq(1)").focus();
				return false;
			}
		}
		var short_name = $(this).find(".short_name").val();
		if(!add_event_history_check($(this).find(".short_name"),short_name)){
			$(this).find(".short_name").focus();
			return false;
		}
		var investor_amount = $(this).find(".investor_amount").val();
		if(!add_event_history_check($(this).find(".investor_amount"),investor_amount)){
			$(this).find(".investor_amount").focus();
			return false;
		}
		//var investor_rate = $(this).find(".investor_rate").val();
		//if(!add_event_history_check($(this).find(".investor_rate"),investor_rate)){
		//	$(this).find(".investor_rate").focus();
		//	return false;
		//}
		//var remark = $(this).find(".remark").val();
		//if(!add_event_history_check($(this).find(".remark"),remark)){
		//	$(this).find(".remark").focus();
		//	return false;
		//}
		
//		var investor_rate = $(this).find(".investor_rate").val();
//		if(!add_event_history_check($(this).find(".investor_rate"),investor_rate)){
//			$(this).find(".investor_rate").focus();
//			return false;
//		}
		
//		var remark = $(this).find(".remark").val();
//		if(!add_event_history_check($(this).find(".remark"),remark)){
//			$(this).find(".remark").focus();
//			return false;
//		}
		
//		var investor_payback = $(this).find(".investor_payback").val();
//		if(!add_event_history_check($(this).find(".investor_payback"),investor_payback)){
//			$(this).find(".investor_payback").focus();
//			return false;
//		}
		
		isSubmit = true;
	});
	
	if(isSubmit){
		return true;
	}else{
		return false;
	}
	
}
//下拉框检查，参数：下拉框ID值
function select_check(selectName) {
	var selectValue = $("#"+selectName).val();
	if (selectValue == 0 || selectValue == "") {
		$('#'+selectName+'_msg').html('必选');
		$("#"+selectName).addClass('warnning');
		return false;
	} else {
		$('#'+selectName+'_msg').html('');
		return true;
	}
}

// 增加投资信息区域
function add_csdq_funds(){
	$("#csdq_hisory_area").append($("#csdq_funds").html());
	var size = (parseInt($("#csdq_hisory_area .csdq_funds").size())) * 4;
	$("#csdq_hisory_2 .item_title").attr("rowspan",size);
	trade_fund_del_btn_show();
}
//删除投资信息
function deal_trade_fund_del(id){
	if(!id){
		alert('无法获取参数信息');return;
	}
	if($("#csdq_hisory_area .csdq_funds").size() == 1){
		alert('至少要有一个投资基金');return;
	}
	if(confirm('确定删除？删除后无法恢复！'))
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=deal_trade_fund_del", 
		data: "id="+id+"&ajax=1",
		dataType: "json",
		success: function(obj){
			if(obj.status == 1){
				$("#csdq_funds_"+id).remove();
				trade_fund_del_btn_show();
			}else{
				alert(obj.info);
			}
		}
	});
}
//
function trade_fund_del_btn_show(){
	if($("#csdq_hisory_area .csdq_funds").size() == 1){
		$("#csdq_hisory_area .csdq_funds:first .csdq_funds_area:last a").hide();
	}else{
		$("#csdq_hisory_area .csdq_funds:first .csdq_funds_area:last a").show();
	}
}
$(document).ready(function() {
	$('input[type=text],input[type=password],textarea,select').live('blur',function() {
		var v = $(this).val();
		if (v != '') {
			$(this).removeClass("warnning");
			$(this).next().html("");
		}
	});
	
	// 是否磁斯达克参与
	$("#csdq_hisory_area").on('change','.is_csdk_fund select',function(){
		var area = $(this).parent().parent().next();
		area.find(".csdq_select_one em").toggle();
		area.find(".csdq_select_two em").toggle();
		if($(this).val() == 1){
			area.find("p").eq(3).find("input").attr("readonly",true).val("");
		}else{
			area.find("p").eq(3).find("input").attr("readonly",false).val("");
		}
	});
	
	// 选择磁斯达克基金，自动填充基金简称
	$("#csdq_hisory_area").on('change','.csdq_select_two select',function(){
		$(this).parent().parent().next().next().find("input").val($(this).find("option:selected").attr("data-value"));
	})
	
	// 删除投资信息区域
	$("#csdq_hisory_area").on('click','.del_csdq_funds',function(){
		if($("#csdq_hisory_area .csdq_funds").size() > 1){
			$(this).parent().parent().remove();
			var size = (parseInt($("#csdq_hisory_area .csdq_funds").size())) * 4;
			$("#csdq_hisory_2 .item_title").attr("rowspan",size);
		}else{
			alert('至少要有一个投资基金');
		}
		trade_fund_del_btn_show();
	})
	
	// 最新轮次，历史轮次转换
	$("#investor_record_type").change(function(){
		var val = $(this).val();
		var type = $(this).attr('data-type');
		if(val == 2){
			$("#csdq_hisory_1,#csdq_hisory_2").show();
		}else{
			// 已有最新轮次，则不能再选择最新轮次
			if(val == 1){
				if(type == 1){
					$(this).next().html('已有最新轮次');
				}
			}
			$("#csdq_hisory_1,#csdq_hisory_2").hide();
			$("input[type=reset]").trigger("click");	// 重置所有信息
			$("#investor_record_type").val(val);	// 回归值
			// 删除多余区域
			$("#csdq_hisory_area .csdq_funds:gt(0)").remove();
			// 重置区域内的警告信息
			$.each($("#csdq_hisory_1 span"),function(){
				$(this).html("");
			})
			$.each($("#csdq_hisory_1 .warnning"),function(){
				$(this).removeClass("warnning");
			})
			$.each($("#csdq_hisory_area .csdq_funds span"),function(){
				$(this).html("");
			})
			$.each($("#csdq_hisory_area .csdq_funds .warnning"),function(){
				$(this).removeClass("warnning");
			})
		}
	});
	
	// 计算融资预估值
	$("#pe_amount_plan,#pe_sell_scale").blur(function(){
		var pe_amount_plan = $("#pe_amount_plan").val();
		var pe_sell_scale = $("#pe_sell_scale").val();
		if(pe_amount_plan && pe_sell_scale){
			$("#pe_evalute").val((pe_amount_plan/pe_sell_scale*100).toFixed(2));
		}
	});
});
