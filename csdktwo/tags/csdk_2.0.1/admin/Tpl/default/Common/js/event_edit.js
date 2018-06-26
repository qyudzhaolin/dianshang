function add_event_check(add_or_edit) {
	
	if (add_or_edit == 'add') {
		var check_json = '[\
			{"name":"investor_amount","min_num":0,"required":true,"number":true,"error_id":"investor_amount_msg","error_msg":"必须填写数字"},\
			{"name":"investor_rate","required":true,"error_id":"investor_rate_msg"},\
			{"name":"evalute_growth_rate","required":true,"error_id":"evalute_growth_rate_msg"},\
			{"name":"investor_payback","required":true,"error_id":"investor_payback_msg"},\
			{"name":"investor_before_evalute","min_num":0,"required":true,"number":true,"error_id":"investor_before_evalute_msg","error_msg":"必须填写数字"},\
			{"name":"investor_after_evalute","min_num":0,"required":true,"number":true,"error_id":"investor_after_evalute_msg","error_msg":"必须填写数字"},\
			{"name":"investor_time","required":true,"error_id":"investor_time_msg"}]';
	} else {
		var check_json = '[\
			{"name":"investor_amount","min_num":0,"required":true,"number":true,"error_id":"investor_amount_msg","error_msg":"必须填写数字"},\
			{"name":"investor_rate","required":true,"error_id":"investor_rate_msg"},\
			{"name":"evalute_growth_rate","required":true,"error_id":"evalute_growth_rate_msg"},\
			{"name":"investor_payback","required":true,"error_id":"investor_payback_msg"},\
			{"name":"investor_before_evalute","min_num":0,"required":true,"number":true,"error_id":"investor_before_evalute_msg","error_msg":"必须填写数字"},\
			{"name":"investor_after_evalute","min_num":0,"required":true,"number":true,"error_id":"investor_after_evalute_msg","error_msg":"必须填写数字"},\
			{"name":"investor_time","required":true,"error_id":"investor_time_msg"}]';
	}
	 
	var check_all = csdk_check(check_json);
	var period_check = select_check('period');
	
	if($('input[name="is_csdk_partake"]:checked').val() == 1){
		var is_display_fund = select_check('partake_fund');
	} else {
		$("#partake_fund").val("");
		var is_display_fund = true;
	}
	
	if (!check_all || !period_check || !is_display_fund) {
		return false;
	}
	return true;
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

function display_select_fund(){
	var radioSeletedValue = $('input[name="is_csdk_partake"]:checked').val();
	if(radioSeletedValue == 1){
		$("#is_display_fund").attr("style","");
	}else{
		$("#is_display_fund").attr("style","display:none");
	}
}

$(document).ready(
		function() {
			$('input[type=text],input[type=password],textarea,select').live('blur',
					function() {
						var v = $(this).val();
						if (v != '') {
							$(this).removeClass("warnning");
						}
					});
			if($("input[name='is_csdk_partake']:checked").val() == 1){
				$("#is_display_fund").attr("style","");
			}else{
				$("#is_display_fund").attr("style","display:none");
			}
			
});


 