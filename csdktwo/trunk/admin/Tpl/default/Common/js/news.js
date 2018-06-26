function news_add(news_id) {

	location.href = ROOT + '?m=News&a=news_add&id=' + news_id;
}
function news_edit(news_id) {

	location.href = ROOT + '?m=News&a=news_edit&id=' + news_id;
}
function news_insert() {
	location.href = ROOT + '?m=News&a=news_insert';
}
function news_update() {
	location.href = ROOT + '?m=News&a=news_update';
}
function news_delete(news_id) {
if(confirm("确定要删除吗？"))
	$.ajax({ 
			url: ROOT + '?m=News&a=news_delete&id=' + news_id,
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
	
}
function news_send(news_id) {
if(confirm("确定要推送吗？"))
	$.ajax({ 
			url: ROOT + '?m=News&a=news_send&id=' + news_id,
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1){
					  location.href=location.href;
                alert("推送成功")
				}else{
					  location.href=location.href;
				 alert("推送失败")	
             
				}	
				
		}
			
	});
	
}
function news_publish(news_id) {
if(confirm("确定要发布吗？"))
$.ajax({ 
			url: ROOT + '?m=News&a=news_publish&id=' + news_id,
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});


}

function cafi_add(news_id) {

	location.href = ROOT + '?m=News&a=cafi_add&id=' + news_id;
}

function cafi_publish(banner_id) {
if(confirm("确定要发布吗？"))
$.ajax({ 
			url:  ROOT + '?m=Banner&a=cafi_publish&id=' + banner_id,
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});

}
function news_cancel(news_id) {
if(confirm("确定要撤回吗？"))
$.ajax({ 
			url:  ROOT + '?m=News&a=news_cancel&id=' + news_id,
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});

}
function cafi_delete(banner_id) {
if(confirm("确定要删除吗？"))
$.ajax({ 
			url: ROOT + '?m=Banner&a=cafi_delete&id=' + banner_id,
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});

	
}

function cafi_edit(banner_id) {

	location.href = ROOT + '?m=News&a=cafi_edit&id=' + banner_id;
}
function cafi_cancel(banner_id) {
if(confirm("确定要撤回吗？"))
$.ajax({ 
			url:  ROOT + '?m=Banner&a=cafi_cancel&id=' + banner_id,
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});



	
}

function add_news_check() {

	var add_news_check = '[{"name":"n_class","required":true,"error_id":"n_class_msg","error_msg":"请填写内容"},\
                        {"name":"n_channel","required":true,"error_id":"n_channel_msg","error_msg":"请填写内容"},\
                        {"name":"n_title","required":true,"error_id":"n_title_msg","error_msg":"请填写内容"},\
                        {"name":"n_app_img","required":true,"error_id":"n_app_img_msg"},\
                        {"name":"n_list_img","required":true,"error_id":"n_list_img_msg"},\
						{"name":"create_time","required":true,"error_id":"create_time_msg"},\
                        {"name":"n_source","required":true,"error_id":"n_source_msg","error_msg":"请填写内容"}]';

	var res = csdk_check(add_news_check);
	
	var brief_check = textarea_check('n_brief');
	
	
//	$('#submitInput').attr("disabled",true);
	if (!res || !brief_check) {
		return false;
	} else {
		return true;
	}
}

function edit_news_check() {

	var edit_news_json = '[{"name":"n_class","required":true,"error_id":"n_class_msg","error_msg":"请填写内容"},\
				    {"name":"n_channel","required":true,"error_id":"n_channel_msg","error_msg":"请填写内容"},\
				    {"name":"n_title","required":true,"error_id":"n_title_msg","error_msg":"请填写内容"},\
				    {"name":"n_app_img","required":true,"error_id":"n_app_img_msg"},\
				    {"name":"n_list_img","required":true,"error_id":"n_list_img_msg"},\
					{"name":"create_time","required":true,"error_id":"create_time_msg"},\
				    {"name":"n_source","required":true,"error_id":"n_source_msg","error_msg":"请填写内容"}]';
	
	var ress = csdk_check(edit_news_json);
	var brief_check = textarea_check('n_brief');
	if (!ress || !brief_check) {
		return false;
	} else {
		return true;
	}
}

function textarea_check(textareaId){
	var textareaValue = $('#' + textareaId).val();
	if (textareaValue == "") {
		$('#'+textareaId+'_msg').html('必填');
		$("#"+textareaId).addClass('warnning');
		return false;
	} else {
		$('#'+textareaId+'_msg').html('');
		return true;
	}
}

//如发布渠道为app，则只显示上传app图片；如发布渠道为pc，则只显示上传pc图片，选择全部，二者都上传
function select_upload_img(is_display){
	$("#b_channel_msg").html("");
	if(is_display == 1){
		// app
		$("#is_display_pc_img").attr("style","display:none;");
		$("#is_display_app_img").attr("style","");
		// 轮播组固定在首页轮播，并且不可选择
		$("#b_bygroup").val("1");
		$("#b_bygroup option:gt(0)").hide();
	}else if(is_display == 2) {
		// web
		$("#is_display_pc_img").attr("style","");
		$("#is_display_app_img").attr("style","display:none;");
		// 轮播组可以任意选择
		$("#b_bygroup option:gt(0)").show();
	}else{
//		$("#is_display_pc_img").attr("style","");
//		$("#is_display_app_img").attr("style","");
		$("#b_channel_msg").html("全部频道已移除");
	}
}
//在线网址
function checkUrl_banner() {
	var patt = /^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/;
	// var deal_url=trim(document.getElementById(id).value);
	var deal_url = $('#b_url').val();
	 
	if (deal_url != '') {
		if (patt.test(deal_url)&& deal_url.indexOf("cisdaq.com")>0) {
			$('#b_url_msg').html('');
			$('#b_url').removeClass("warnning");
			return true;
		} else {
			$('#b_url_msg').html('链接网址格式不正确');
			$('#b_url').addClass('warnning');
			$('#b_url').focus();
			return false;
		}

	} else {
		return true;
	}
	
}
function add_banner_check(){
	var add_banner_check ='[{"name":"b_channel","required":true,"error_id":"b_channel_msg","error_msg":"请填写内容"},\
                         {"name":"b_title","required":true,"error_id":"b_title_msg","error_msg":"请填写内容"},\
                         {"name":"b_sort","required":true,"error_id":"b_sort_msg","error_msg":"请填写内容"},\
                         {"name":"b_bygroup","required":true,"error_id":"b_bygroup_msg","error_msg":"必填项"}]';
	var res = csdk_check(add_banner_check);
	 
	var url_check = checkUrl_banner();
	// 检查发布渠道，如为app则只上传app图片，如为pc则上传pc图片
	// 1:app 2:pc 3:全部
	var pc_img_check = true;
	var app_img_check = true;
	if($("#b_channel").val() == 1){
		app_img_check = check_deal_field('b_app_img', 1, 0);
		$("#b_pc_img").val('');
	}else if($("#b_channel").val() == 2){
		pc_img_check = check_deal_field('b_pc_img', 1, 0);
		$("#b_app_img").val('');
	}else{
		$("#b_channel_msg").html("全部频道已移除");
		return false;
//		 pc_img_check = check_deal_field('b_pc_img', 1, 0);
//		 app_img_check = check_deal_field('b_app_img', 1, 0);
	}
    if (!res || !app_img_check || !pc_img_check || !url_check) {
		return false;
	} else {
		return true;


	}
}

function checkUrl(url) {
	var patt = /^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/;
	// var deal_url=trim(document.getElementById(id).value);
	var url_val = $('#'+url).val();
	if (url_val == '') {
		$('#'+url+'_msg').html('必填');
		$('#'+url).addClass('warnning');
		$('#'+url).focus();
		return false;
	} else {
		if (patt.test(url_val)) {
			$('#'+url+'_msg').html('');
			return true;
		} else {
			$('#'+url+'_msg').html('请输入正确的URL地址格式');
			$('#'+url).addClass('warnning');
			$('#'+url).focus();
			return false;
		}

	}

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
	select_upload_img($("#b_channel").val());
});