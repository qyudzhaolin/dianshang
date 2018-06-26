function user_audit(id){
	location.href = ROOT + '?m=User&a=user_detail&id='+id+'&audit=1';
}

function audit_user_check(){
	var json='[\
		{"name":"audit_desc","max_len":30,"required":true,"error_id":"audit_desc_msg","error_msg":"请确保内容在30个字以内"} \
		]';
	
	var res = csdk_check(json);
	if(!res){
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
