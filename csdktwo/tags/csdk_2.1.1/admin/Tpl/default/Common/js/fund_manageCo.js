function mt_check_field(field_id, is_require, max_len, img_file_id) {
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

function mc_add(){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageCo_add";
}
function ms_add(mid){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageShare_add&mid="+mid;
}
function mt_add(mid){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageTeam_add&mid="+mid;
}

function mc_edit(id){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageCo_edit&id="+id;
}
function ms_edit(id){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageShare_edit&id="+id;
}
function mt_edit(id){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageTeam_edit&id="+id;
}

function ms_index(mid){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageShare_index&mid="+mid;
}
function mt_index(mid){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageTeam_index&mid="+mid;
}

function mt_user_search(){
	var height = $(window).height() - 95;
	$.weeboxs.open('<div id="mt_user_area"></div>', {
		title:'选择公司管理团队成员', 
		width:800,
		height:height,
		showButton:false,
		onclose:function(){
			
		}
	});
	var html = $("#mt_main").clone(true);
	html.find('.mt_user_list').attr('id','mt_user_list');
	html.find('input[name="user_name"]').attr('id','user_name');
	html.find('input[name="org_title"]').attr('id','org_title');
	html.attr('style','display:block');
	$("#mt_user_area").append(html);
	mt_get_user();
}

function mt_get_user(user_name,org_title,p){
	user_name = user_name || "";
	org_title = org_title || "";
	p = p || 1;
	var mid = parseInt($("#managers_id").val());
	$.ajax({
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=manageTeam_add_user_search", 
		data: "mid="+mid+"&user_name="+user_name+"&org_title="+org_title+"&p="+p+"&ajax=1",
		dataType: "text",
		beforeSend: function () {
			$("#mt_user_list").html("loading...");
		},
		success: function(obj){
			$("#mt_user_list").html(obj);
		},
		error:function(){
			alert('error');
		}
	});
}

function mt_user_select(id,can_,domobj){
	if(!id) return;
	
	if(can_ == 'has'){
		alert('会员 '+$(domobj).parent().prev().prev().text()+' 已是该公司的团队成员，请重新选择');
		return;
	} else if(can_ > 0){
		// 已删除的成员
		$.ajax({
			url:ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=get_manageTeam_info_by_id", 
			data: "id="+can_+"&ajax=1",
			dataType:"json",
			success: function(obj){
				$("#code").val(obj.info.code);
				$("#name").val(obj.info.user_name);
				$("#user_id").val(obj.info.user_id);
				$("#img_src").attr('src',obj.info.real_logo);
				$("#user_logo").val(obj.info.user_logo);
				$("#title").val(obj.info.title);
				$("#graduate_university").val(obj.info.graduate_university);
				$("#education_degree").val(obj.info.education_degree);
				$("#brief").val(obj.info.brief);
				$("#id_exist").val(obj.info.id);
				$("body").trigger('click');
			},
			error:function(){
				alert('error');
			}
		});
		return;
	} else {
		$.ajax({
			url:ROOT+"?"+VAR_MODULE+"=User&"+VAR_ACTION+"=get_user_info", 
			data: "id="+id+"&ajax=1",
			dataType:"json",
			success: function(obj){
				$("#code").val($("#code").attr('data-value'));
				$("#name").val(obj.data.user_name);
				$("#user_id").val(obj.data.id);
				$("#img_src").attr('src',obj.data.real_logo);
				$("#user_logo").val(obj.data.img_user_logo);
				$("#title").val("");
				$("#graduate_university").val("");
				$("#education_degree").val(obj.data.per_degree);
				$("#brief").val("");
				$("#id_exist").val("");
				$("body").trigger('click');
			},
			error:function(){
				alert('error');
			}
		});
	}
}

function mc_check(){
	var check_json='[\
        {"name":"name","required":true,"max_len":50,"error_id":"name_msg","error_msg":"公司全称不超50个字"},\
        {"name":"short_name","required":true,"max_len":6,"error_id":"short_name_msg","error_msg":"公司简称不超6个字"},\
        {"name":"legal_person","required":true,"max_len":20,"error_id":"legal_person_msg","error_msg":"法定代表人不超20个字"},\
        {"name":"reg_found","required":true,"max_len":10,"error_id":"reg_found_msg","error_msg":"必须填数字,最大10位"},\
        {"name":"com_time","required":true,"error_id":"com_time_msg"},\
        {"name":"registration_no","required":true,"max_len":20,"error_id":"registration_no_msg","error_msg":"工商注册号不超20位"},\
        {"name":"registration_address","required":true,"max_len":100,"error_id":"registration_address_msg","error_msg":"办公地址不超100个字"},\
        ]';
	var check_all = csdk_check(check_json);
	if(!check_all){
		return false;
	}else{
		return true;
	}
}
function ms_check(){
	var check_json='[\
		{"name":"name","required":true,"max_len":20,"error_id":"name_msg","error_msg":"股东名称不超20个字"},\
		{"name":"share","required":true,"min_num":0,"max_num":100,"error_id":"share_msg","error_msg":"必填"},\
		{"name":"remark","required":true,"max_len":50,"error_id":"remark_msg","error_msg":"备注不超20个字"},\
		]';
	var check_all = csdk_check(check_json);
	if(!check_all){
		return false;
	}else{
		return true;
	}
}
function mt_check(){
	var check_json='[\
		{"name":"title","required":true,"error_id":"title_msg","max_len":6,"error_msg":"职务名称不超6个字"},\
		{"name":"graduate_university","required":true,"max_len":20,"error_id":"graduate_university_msg","error_msg":"毕业院校不超20个字"},\
		{"name":"education_degree","required":true,"error_id":"education_degree_msg"},\
		{"name":"brief","required":true,"max_len":200,"error_id":"brief_msg","error_msg":"个人简介不超200个字"},\
		]';
	var check_all = csdk_check(check_json);
	var user_logo_check = mt_check_field('user_logo', 1, 0, 'img_src');
	var user_id_check = mt_check_field('user_id', 1, 0, 'name');
	if(!check_all || !user_logo_check || !user_id_check){
		return false;
	}else{
		return true;
	}
}

function mc_del(id){
	if(!id)
	{
		alert("请选择需要删除的基金管理公司");
		return;
	}
	if(confirm("确定要删除选中的基金管理公司吗？"))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=manageCo_del&id="+id, 
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
function mc_restore(id){
	if(!id)
	{
		alert("请选择需要恢复的基金管理公司");
		return;
	}
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=manageCo_restore&id="+id, 
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
function ms_del(id){
	if(!id)
	{
		alert("请选择需要删除的公司股份构成");
		return;
	}
	if(confirm("确定要删除选中的公司股份构成吗？"))
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=manageShare_del&id="+id, 
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
function mt_del(id){
	if(!id)
	{
		alert("请选择需要删除的公司团队成员");
		return;
	}
	if(confirm("确定要删除选中的公司团队成员吗？"))
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=manageTeam_del&id="+id, 
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
 	$(".main .search_row").on("click","#mt_user_button",function(e){
 		var user_name = $.trim($("#user_name").val());
 		var org_title = $.trim($("#org_title").val());
 		if(user_name == '' && org_title == ""){
 			return;
 		}
 		mt_get_user(user_name,org_title);
 	})
 	$(".main .search_row").on("click","#mt_user_reset",function(e){
 		$("#user_name").val("");
 		$("#org_title").val("");
 		mt_get_user();
 	})
 	$(".main .mt_user_list").on("click",".page a",function(e){
 		e.preventDefault();
 		var href = $(this).attr("href").split('&');
 		console.log(href);
 		var user_name = href[3].split('=');
 		var org_title = href[4].split('=');
 		var p = href[6].split('=');
 		mt_get_user(user_name[1],org_title[1],p[1]);
 	})
});