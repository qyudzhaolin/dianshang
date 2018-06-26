function attach_check_field(field_id, is_require, max_len, img_file_id) {
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

function attach_add(fid){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=attachment_add&fid="+fid;
}

function attach_edit(id){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=attachment_edit&id="+id;
}

function attach_see(mid){
	
}

function attach_check(){
	var check_json='[\
        {"name":"title","required":true,"max_len":30,"error_id":"title_msg","error_msg":"信息标题不超30个字"},\
        {"name":"publish_time","required":true,"error_id":"publish_time_msg"},\
        {"name":"remark","required":true,"max_len":200,"error_id":"remark_msg","error_msg":"备注不超200位"},\
        ]';
	var check_all = csdk_check(check_json);
	var attachment_check = attach_check_field('attachment', 1, 0,'img_src');
	if(!check_all || !attachment_check){
		return false;
	}else{
		return true;
	}
}

function attach_del(id){
	if(!id)
	{
		alert("请选择需要删除的基金披露信息");
		return;
	}
	if(confirm("删除后，前段将无法查看此信息，确定要删除吗？"))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=attachment_del&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				if(obj.status==1)
					location.href=location.href;
				else
					alert(obj.info);
			}
	});
}

$(document).ready(function() {
 	$('input[type=text],input[type=password],textarea').live('blur',function(){
 		var v=$(this).val();
		if (v!=''){
			$(this).removeClass("warnning");
		}
	});
});