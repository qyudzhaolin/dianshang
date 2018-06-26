//  ###################################################
//	公共文件，修改需谨慎
//  ###################################################

/*
 * 项目关注
 * 参数：deal_id 项目ID 
 * field 作用域
 * 要求：需要在 field 上添加 data-count 属性，并赋值为当前关注数
 */
function do_focus(deal_id,field){
	var deal_id = parseInt(deal_id);
	if(!deal_id || isNaN(deal_id)){
		alert('error:无法获取项目信息');
		return;
	}
	var this_ = $("#"+field);
	var count = parseInt(this_.attr("data-count"));
	count = isNaN(count) ? 1 : count + 1;
	var ajaxTimeoutTest = $.ajax({ 
		url: APP_ROOT+"/index.php?ctl=investors&act=attention",
		dataType: "json",
		data:"id="+deal_id,
		type: "POST",
		beforeSend: function () {
			this_.attr('onclick','');
		},
		complete: function (XMLHttpRequest,status) {
			if(status == 'timeout'){
				ajaxTimeoutTest.abort();
				alert('发送失败');
				this_.attr('onclick','javascript:do_focus('+deal_id+',"'+field+'")');
			}
		},
		success: function(obj){
			if(obj.status == 100){
				this_.addClass('active');
				this_.html("关注&nbsp;"+ count);
				this_.attr('onclick','');
			}else{
				alert(obj.info);
			}
		},
		error:function(ajaxobj){
			alert('发送失败');
			this_.attr('onclick','javascript:do_focus('+deal_id+',"'+field+'")');
		}

	});
}