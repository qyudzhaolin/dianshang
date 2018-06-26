 

function add_investor_check(add_or_edit) {
	
	if (add_or_edit == 'add') {
		var check_json = '[\
			{"name":"investor_amount","min_num":0,"required":true,"number":true,"error_id":"investor_amount_msg","error_msg":"必须填写数字"},\
			{"name":"investor_rate","required":true,"error_id":"investor_rate_msg"},\
			{"name":"name","required":true,"error_id":"name_msg"},\
			{"name":"s_name","required":true,"error_id":"s_name_msg"}]';
	} else {
		var check_json = '[\
			{"name":"investor_amount","min_num":0,"required":true,"number":true,"error_id":"investor_amount_msg","error_msg":"必须填写数字"},\
			{"name":"investor_rate","required":true,"error_id":"investor_rate_msg"},\
			{"name":"name","required":true,"error_id":"name_msg"},\
			{"name":"s_name","required":true,"error_id":"s_name_msg"}]';
	}
	 
	var check_all = csdk_check(check_json);
 
	if (!check_all ) {
		return false;
	}

	return true;
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
});
function changefund(iii)
{ 
    var k2 = 2;
 for(var j2 = 0;j2 < k2;j2++){
     if(j2 == iii){
      document.all["Divt" + j2].style.display = "";
  }
  else{
      document.all["Divt" + j2].style.display = "none";
  }
 }
}
 