 

 
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
 
 
function add_fund_check(add_or_edit){
	if (add_or_edit=='add') {
		// // {"name":"code","required":true,"max_len":10,"error_id":"code_msg","error_msg":"基金编码不超10位"},\
		var check_json='[\
            {"name":"name","required":true,"max_len":30,"error_id":"name_msg","error_msg":"基金名称不超30位"},\
            {"name":"short_name","required":true,"max_len":6,"error_id":"short_name_msg","error_msg":"基金短名称不超6个字"},\
            {"name":"manager","required":true,"error_id":"manager_msg"},\
            {"name":"total_amount","min_num":0,"required":true,"number":true,"error_id":"total_amount_msg","error_msg":"必须填数字"},\
            {"name":"establish_date","required":true,"error_id":"establish_date_msg"},\
            {"name":"deadline","required":true,"error_id":"deadline_msg"},\
            {"name":"summary","required":true,"error_id":"summary_msg"},\
            {"name":"max_payback","required":true,"number":true,"error_id":"max_payback_msg","error_msg":"必须填数字"},\
            {"name":"average_payback","required":true,"number":true,"error_id":"average_payback_msg","error_msg":"必须填数字"},\
            {"name":"total_payback","required":true,"number":true,"error_id":"total_payback_msg","error_msg":"必须填数字"},\
            {"name":"add_team","box":1,"required":true,"error_id":"team_msg",\
                "input":[{"name":"team_name[]","required":true,"max_len":6,"error_msg":"请确保姓名在6个字以内"},\
                        {"name":"title[]","required":true,"max_len":8,"error_msg":"请确保职位在8个字以内"},\
                        {"name":"brief[]","required":true,"max_len":200,"error_msg":"请确保内容在200个字以内"}]},\
            ]';
	}else{
		var check_json='[\
        {"name":"name","required":true,"max_len":30,"error_id":"name_msg","error_msg":"基金名称不超30位"},\
        {"name":"short_name","required":true,"max_len":6,"error_id":"short_name_msg","error_msg":"基金短名称不超6个字"},\
        {"name":"manager","required":true,"error_id":"manager_msg"},\
        {"name":"total_amount","min_num":0,"required":true,"number":true,"error_id":"total_amount_msg","error_msg":"必须填数字"},\
        {"name":"establish_date","required":true,"error_id":"establish_date_msg"},\
        {"name":"deadline","required":true,"error_id":"deadline_msg"},\
        {"name":"summary","required":true,"error_id":"summary_msg"},\
        {"name":"max_payback","required":true,"number":true,"error_id":"max_payback_msg","error_msg":"必须填数字"},\
        {"name":"average_payback","required":true,"number":true,"error_id":"average_payback_msg","error_msg":"必须填数字"},\
        {"name":"total_payback","required":true,"number":true,"error_id":"total_payback_msg","error_msg":"必须填数字"},\
        {"name":"add_team","box":1,"required":true,"error_id":"team_msg",\
            "input":[{"name":"team_name[]","required":true,"max_len":6,"error_msg":"请确保姓名在6个字以内"},\
					{"name":"title[]","required":true,"max_len":8,"error_msg":"请确保职位在8个字以内"},\
                    {"name":"brief[]","required":true,"max_len":200,"error_msg":"请确保内容在200个字以内"}]},\
        ]';
	}
	var check_all=csdk_check(check_json);
	if(!check_all){
		return false;
	}else{
		return true;
	}
}


function add_fund_investor_check(add_or_edit){
	if(add_or_edit == 'add'){
		var check_json='[\
			{"name":"user_name","required":true,"error_id":"user_id_msg"},\
			{"name":"investor_amount","required":true, "number":true,"error_id":"investor_amount_msg","error_msg":"必须填数字"},\
			{"name":"investor_rate","required":true,"number":true,"error_id":"investor_rate_msg","error_msg":"必须填数字"},\
			{"name":"create_time","required":true,"error_id":"create_time_msg"},\
	    ]';
	} else{
		var check_json='[\
			{"name":"user_name","required":true,"error_id":"user_id_msg"},\
			{"name":"investor_amount","required":true, "number":true,"error_id":"investor_amount_msg","error_msg":"必须填数字"},\
			{"name":"investor_rate","required":true,"number":true,"error_id":"investor_rate_msg","error_msg":"必须填数字"},\
			{"name":"create_time","required":true,"error_id":"create_time_msg"},\
	    ]';
	}

    var check_all=csdk_check(check_json);
	if(!check_all){
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

$(document).ready(function() {
 	$('input[type=text],input[type=password],textarea').live('blur',function(){
 		var v=$(this).val();
		if (v!=''){
			$(this).removeClass("warnning");
		}
	});
});