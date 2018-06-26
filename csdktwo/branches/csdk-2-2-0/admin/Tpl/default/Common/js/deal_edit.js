//切换地区
$(document).ready(function() {
	$("select[name='province']").bind("change", function() {
		load_city();
	});
	$("input[name='user_id']").bind("blur", function() {
		check_user();
	});
});
function load_city() {
	var id = $("select[name='province']").find("option:selected").attr("rel");

	var evalStr = "regionConf.r" + id + ".c";

	if (id == 0) {
		var html = "<option value=''>请选择城市</option>";
	} else {
		var regionConfs = eval(evalStr);
		evalStr += ".";
		var html = "<option value=''>请选择城市</option>";
		for ( var key in regionConfs) {
			html += "<option value='" + eval(evalStr + key + ".i") + "' rel='"
			+ eval(evalStr + key + ".i") + "'>"
			+ eval(evalStr + key + ".n") + "</option>";
		}
	}
	$("select[name='city']").html(html);
}

function check_user() {
	var user_id = $("input[name='user_id']").val();
	if (!isNaN(user_id) && parseInt(user_id) > 0) {
		$.ajax({
			url : ROOT + "?" + VAR_MODULE + "=User&" + VAR_ACTION
			+ "=check_user&id=" + user_id,
			data : "ajax=1",
			dataType : "json",
			success : function(obj) {

				if (!obj.status) {
					alert("会员ID不存在");
					$("input[name='user_id']").val('');
				}

			}
		});
	} else {
		$("input[name='user_id']").val('');
	}
}


function add_event() {

	var len = $("input[name=create_time_event[]]").size();
	/*
	 * if (len>=3) { alert("只能上传3条"); return; };
	 */
	$.ajax({
		url : ROOT + "?" + VAR_MODULE + "=Deal&" + VAR_ACTION
		+ "=add_event&len=" + len,
		data : "ajax=1",
		success : function(obj) {
			$("#faq_event").append(obj);
		}
	});
}
function add_trade_event() {

	var len = $("input[name=create_time_trade_event[]]").size();
	$.ajax({
		url : ROOT + "?" + VAR_MODULE + "=Deal&" + VAR_ACTION
		+ "=add_trade_event&len=" + len,
		data : "ajax=1",
		success : function(obj) {
			$("#trade_event").append(obj);
		}
	});
}
function add_point_event() {

	var len = $("input[name=point_event[]]").size();
	if (len >= 3) {
		alert("只能上传3条");
		return;
	}
	;
	$.ajax({
		url : ROOT + "?" + VAR_MODULE + "=Deal&" + VAR_ACTION
		+ "=add_point_event&len=" + len,
		data : "ajax=1",
		success : function(obj) {
			$("#point_event").append(obj);
		}
	});
}
function add_user_point_event() {

	var len = $("input[name=user_point_event[]]").size();
	if (len >= 3) {
		alert("只能上传3条");
		return;
	}
	;
	$.ajax({
		url : ROOT + "?" + VAR_MODULE + "=Deal&" + VAR_ACTION
		+ "=add_user_point_event&len=" + len,
		data : "ajax=1",
		success : function(obj) {
			$("#user_point_event").append(obj);
		}
	});
}
function del_event(o) {
	$(o).parent().remove();
}
function check_deal_field(field_id, is_require, max_len, img_file_id) {
	var field = $("[name=" + field_id + "]").val();
	field = trim(field);
	if (is_require == 1 && field == '') {
		$("#" + field_id + "_msg").html('必填');
		if (img_file_id != '') {
			$("#" + img_file_id).addClass('warnning');
		} else {
			$("[name=" + field_id + "]").addClass('warnning');
		}

		$("[name=" + field_id + "]").focus();
		return false;
	} else {
		if (max_len != 0 && field.length > max_len) {		
			$("#" + field_id + "_msg").html('请确保内容在' + max_len + '个字以内');
			$("[name=" + field_id + "]").addClass('warnning');
			$("[name=" + field_id + "]").focus();
			return false;
		} else {
			$("#" + field_id + "_msg").html('');
			return true;
		}
	}
}
//选择方向
function checkCate() {
	var cate_check = 0;
	$("[name='cate_choose[]']").each(function() {
		if ($(this).attr("checked") == 'checked') {
			cate_check = cate_check + 1;
		}
	});
	if (cate_check == 0) {
		$('#cate_choose_msg').html('必选');
		// $("#cates").addClass('warnning');
		return false;
	} else {
		$('#cate_choose_msg').html('');
		return true;
	}
}

//选择阶段
function checkPeriod() {
	var period_check = $.trim($("#period_id").val());
	if (period_check == 0 || period_check == "") {
		$('#period_id_msg').html('必选');
		$("#periods").addClass('warnning');
		return false;
	} else {
		$('#period_id_msg').html('');
		return true;
	}
}
//选择推荐人
function checkUser() {
	var user_check = $("#user_id").val();
	if (user_check == 0 || user_check == "") {
		$('#user_id_msg').html('必选');
		$("#users").addClass('warnning');
		return false;
	} else {
		$('#user_id_msg').html('');
		return true;
	}
}
//选择项目可见度
function checkVis() {
	var vis_check = $("#vis").val();
	if (vis_check == 0 || vis_check == "") {
		$('#vis_msg').html('必选');
		$("#viss").addClass('warnning');
		return false;
	} else {
		$('#vis_msg').html('');
		return true;
	}
}
//选择是否精品项目
function checkBest() {
	var is_best_check = $("#is_best").val();
	if ( is_best_check == "") {
		$('#is_best_msg').html('必选');
		$("#is_bests").addClass('warnning');
		return false;
	} else {
		$('#is_best_msg').html('');
		return true;
	}
}
//在线网址
function checkUrl() {
	var patt = /^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/;
	// var deal_url=trim(document.getElementById(id).value);
	var deal_url = $('#deal_url').val();
	if (deal_url == '') {
		$('#deal_url_msg').html('必填');
		$('#deal_url').addClass('warnning');
		$('#deal_url').focus();
		// $('#deal_url').focus();
		return false;
	} else {
		if (patt.test(deal_url)) {
			$('#deal_url_msg').html('');
			return true;
		} else {
			$('#deal_url_msg').html('在线网址格式不正确');
			$('#deal_url').addClass('warnning');
			$('#deal_url').focus();
			return false;
		}

	}

}
//公司官网
function checkcomUrl() {
	var patt = /^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/;
	// var deal_url=trim(document.getElementById(id).value);
	var com_web = $('#com_web').val();
	if (com_web == '') {
		$('#com_web_msg').html('必填');
		$('#com_web').addClass('warnning');
		$('#com_web').focus();
		// $('#deal_url').focus();
		return false;
	} else {
		if (patt.test(com_web)) {
			$('#com_web_msg').html('');
			return true;
		} else {
			$('#com_web_msg').html('公司官网格式不正确');
			$('#com_web').addClass('warnning');
			$('#com_web').focus();
			return false;
		}

	}

}
function add_deal_check(add_or_edit) {

	if (add_or_edit == 'add') {
		var check_json = '[\
			{"name":"name","required":true,"max_len":14,"error_id":"name_msg","error_msg":"请确保内容在14个字以内"},\
			{"name":"s_name","required":true,"max_len":6,"error_id":"s_name_msg","error_msg":"请确保内容在6个字以内"},\
			{"name":"com_reg_found","required":true,"number":true,"error_id":"com_reg_found_msg","error_msg":"必须填写数字"},\
			{"name":"deal_sign","required":true,"max_len":28,"error_id":"deal_sign_msg","error_msg":"请确保内容在28个字以内"},\
			{"name":"deal_brief","required":true,"max_len":400,"error_id":"deal_brief_msg","error_msg":"请确保内容在400个字以内"},\
			{"name":"company_name","required":true,"error_id":"company_name_msg"},\
			{"name":"com_legal","required":true,"error_id":"com_legal_msg"},\
			{"name":"com_tel","required":true,"error_id":"com_tel_msg"},\
			{"name":"com_addr","required":true,"error_id":"com_addr_msg"},\
			{"name":"com_web","required":true,"error_id":"com_web_msg"},\
			{"name":"com_busi","required":true,"error_id":"com_busi_msg"},\
			{"name":"com_reg_found","required":true,"error_id":"com_reg_found_msg"},\
			{"name":"com_time","required":true,"error_id":"com_time_msg"},\
			{"name":"out_plan","required":true,"error_id":"out_plan_msg"},\
			{"name":"company_brief","max_len":200 ,"required":true,"error_id":"company_brief_msg","error_msg":"请确保内容在200个字以内"},\
			{"name":"com_legal","required":true,"max_len":200,"error_id":"com_legal_msg","error_msg":"请确保内容在200个字以内"},\
			{"name":"add_team","box":1,"required":true,"error_id":"team_msg",\
			"input":[{"name":"team_name[]","required":true,"error_msg":"请确保内容在6个字以内"},\
			{"name":"title[]","required":true,"error_msg":"请确保内容在8个字以内"},\
			{"name":"brief[]","required":true,"error_msg":"请确保内容在200个字以内"}]},\
			]';
		/*{"name":"add_user_point_event","box":1,"required":true,"error_id":"add_user_point_msg",\
			"input":[{"name":"user_point_event[]","required":true,"error_msg":"必填"}]},\
		{"name":"pe_amount_plan","min_num":0,"required":true,"number":true,"error_id":"pe_amount_plan_msg","error_msg":"必须填写数字"},\
		{"name":"pe_least_amount","min_num":99,"required":true,"number":true,"error_id":"pe_least_amount_msg","error_msg":"必须填写数字"},\
		{"name":"financing_amount","required":true,"number":true,"error_id":"financing_amount_msg","error_msg":"必须填写数字"},\
		{"name":"pe_sell_scale","required":true,"error_id":"pe_sell_scale_msg"},\
			{"name":"add_point_event","box":1,"required":true,"error_id":"add_point_event_msg",\
			"input":[{"name":"point_event[]","required":true,"error_msg":"必填"}]},\
			{"name":"add_event","box":1,"required":true,"error_id":"create_time_event_msg",\
			"input":[{"name":"create_time_event[]","required":true,"error_msg":"请选择日期"},\
			{"name":"brief_event[]","required":true,"max_len":14,"error_msg":"请确保内容在14个字以内"}]},\*/
	} else {
		/*{"name":"pe_amount_plan","min_num":0,"required":true,"number":true,"error_id":"pe_amount_plan_msg","error_msg":"必须填写数字"},
		{"name":"pe_sell_scale","required":true,"error_id":"pe_sell_scale_msg"},\
		{"name":"financing_amount","required":true,"number":true,"error_id":"financing_amount_msg","error_msg":"必须填写数字"},\
		{"name":"entry_info","required":true,"max_len":400,"error_id":"entry_info_msg","error_msg":"请确保内容在400个字以内"},\
		{"name":"profession_info","required":true,"max_len":400,"error_id":"profession_info_msg","error_msg":"请确保内容在400个字以内"},\
		{"name":"vision_info","required":true,"max_len":400,"error_id":"vision_info_msg","error_msg":"请确保内容在400个字以内"},\
		{"name":"deal_operation_steps_event","box":1,"required":true,"error_id":"deal_operation_steps_msg",\
			{"name":"opera_steps_name[]","required":true,"error_msg":"必填"},
			{"name":"opera_steps_brief[]","required":true,"error_msg":"必填"}]},
		{"name":"operation_info","required":true,"max_len":200,"error_id":"operation_info_msg","error_msg":"请确保内容在200个字以内"},\
		{"name":"pe_least_amount","min_num":99,"required":true,"number":true,"error_id":"pe_least_amount_msg","error_msg":"必须填写数字"},\\*/
		var check_json = '[\
			{"name":"name","required":true,"max_len":14,"error_id":"name_msg","error_msg":"请确保内容在14个字以内"},\
			{"name":"s_name","required":true,"max_len":6,"error_id":"s_name_msg","error_msg":"请确保内容在6个字以内"},\
			{"name":"com_reg_found","required":true,"number":true,"error_id":"com_reg_found_msg","error_msg":"必须填写数字"},\
			{"name":"deal_sign","required":true,"max_len":28,"error_id":"deal_sign_msg","error_msg":"请确保内容在28个字以内"},\
			{"name":"deal_brief","required":true,"max_len":200,"error_id":"deal_brief_msg","error_msg":"请确保内容在200个字以内"},\
			{"name":"company_name","required":true,"error_id":"company_name_msg"},\
			{"name":"com_legal","required":true,"error_id":"com_legal_msg"},\
			{"name":"com_tel","required":true,"error_id":"com_tel_msg"},\
			{"name":"com_addr","required":true,"error_id":"com_addr_msg"},\
			{"name":"com_web","required":true,"error_id":"com_web_msg"},\
			{"name":"com_busi","required":true,"error_id":"com_busi_msg"},\
			{"name":"com_reg_found","required":true,"error_id":"com_reg_found_msg"},\
			{"name":"com_time","required":true,"error_id":"com_time_msg"},\
			{"name":"out_plan","required":true,"error_id":"out_plan_msg"},\
			{"name":"company_brief","max_len":200 ,"required":true,"error_id":"company_brief_msg","error_msg":"请确保内容在200个字以内"},\
			{"name":"add_team","box":1,"required":true,"error_id":"team_msg",\
			"input":[{"name":"team_name[]","required":true,"error_msg":"请确保内容在6个字以内"},\
			{"name":"title[]","required":true,"error_msg":"请确保内容在8个字以内"},\
			{"name":"brief[]","required":true,"error_msg":"请确保内容在200个字以内"}]},\
			{"name":"deal_data_img_event","box":1,"required":true,"error_id":"deal_data_img_event_msg",\
			"input":[{"name":"img_data_url[]","required":true,"error_msg":"图片必填"}]},\
			]';
	}
	var check_all = csdk_check(check_json);
	// var bp_url_check = check_deal_field('bp_url', 1, 0, 'bp');
	var deal_url_check = check_deal_field('deal_url', 1, 0);
	var com_web_check = check_deal_field('com_web', 1, 0);
	var province_check = check_deal_field('province', 1, 0);
	var city_check = check_deal_field('city', 1, 0);
	var img_deal_logo_check = check_deal_field('img_deal_logo', 1, 0,'avatar_file');
	var img_deal_cover_check = add_or_edit == 'add' ? true : check_deal_field('img_deal_cover', 1, 0,'img_deal_cover_file');
	var img_deal_app_cover_check = add_or_edit == 'add' ? true : check_deal_field('img_deal_app_cover', 1, 0,'img_deal_app_cover_file');
	if($('#is_case').val() == 1){
		var capital_check = check_deal_field('capital', 1, 200);
	}else{
		$('#capital').val('');
		var capital_check = true;
	}
	var url_check = checkUrl();
	var com_web_check = checkcomUrl();
	var cate_check = checkCate();
	var period_check = add_or_edit == 'add' ? checkPeriod() : true;
	var user_check = checkUser();
	var vis_check = checkVis();
	var is_best_check = checkBest();

	// 检查商业模式 图片必填
	var isSub = true;
	if(add_or_edit != 'add'){
		$.each($("#deal_operation_steps_faq input[type=file]"),function(i,n){
			isSub = false;
			if($(this).next().val() == ""){
				$(this).next().next().css("border","1px solid red");
				$("#deal_operation_steps_msg").html("图片必填");
				$(this).focus();
				return false;
			}else{
				isSub = true;
				$("#deal_operation_steps_msg").html("");
				$(this).next().next().css("border","1px solid #ccc");
			}
		})
	}

	if(!img_deal_logo_check || !img_deal_cover_check || !img_deal_app_cover_check){
		$('body,html').animate({scrollTop:0},500)
		return false;
	}
	if (!deal_url_check || !com_web || !province_check || !period_check
			|| !check_all || !url_check|| !com_web_check || !cate_check
			|| !user_check || !vis_check || !is_best_check || !capital_check || !city_check || !isSub) {
		return false;
	}

	return true;

}
//如为成功案例，则需要维护投资回报分析
function display_capital(is_display){
	if(is_display == 1){
		$("#is_display_capital").attr("style","");
	}else{
		$("#is_display_capital").attr("style","display:none;");
	}
}

// 所属行业
function  cate_choose_check(obj){
	var size = $("input[tag=cate_choose]:checked").size()
	if(size == 3){
		$("input[tag=cate_choose]:not(:checked)").attr("disabled",true);
	}else if(size > 3){
		obj.checked = false;
		$("input[tag=cate_choose]:not(:checked)").attr("disabled",true);
		alert("最多三个");
	}else{
		$("input[tag=cate_choose]:not(:checked)").attr("disabled",false);
	}
}

$(document).ready(function() {
	$('input[type=text],input[type=password],textarea').live('blur',function() {
		var v = $(this).val();
		if (v != '') {
			$(this).removeClass("warnning");
		}
	});
	if($('#is_case').val() == 1){
		$("#is_display_capital").attr("style","");
	}
	
	
});