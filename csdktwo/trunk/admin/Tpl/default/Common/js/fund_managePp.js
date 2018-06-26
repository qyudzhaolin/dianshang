// +----------------------------------------------------------------------
// | 管理基金拟投项目
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

function mpp_add(){
	var height = $(window).height() - 95;
	$.weeboxs.open('<div id="mpp_area"></div>', {
		title:'选择拟投项目', 
		width:800,
		height:height,
		showButton:false,
		onclose:function(){
			
		}
	});
	var html = $("#mpp_main").clone(true);
	html.find('.mpp_list').attr('id','mpp_list');
	html.find('input[name="name"]').attr('id','name');
	html.attr('style','display:block');
	$("#mpp_area").append(html);
	mpp_get_deal();
}

function mpp_get_deal(name,p){
	name = name || "";
	p = p || 1;
	$.ajax({
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=pp_add_search_deal", 
		data: "fid="+$("#id").val()+"&name="+name+"&p="+p+"&ajax=1",
		dataType: "text",
		beforeSend: function () {
			$("#mpp_list").html("loading...");
		},
		success: function(obj){
			$("#mpp_list").html(obj);
		},
		error:function(){
			alert('error');
		}
	});
}

function mpp_deal_select(deal_id,obj){
	$.ajax({
		url:ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=do_pp_add", 
		data: "fid="+$("#id").val()+"&deal_id="+deal_id+"&ajax=1",
		dataType:"json",
		beforeSend: function () {
			$(obj).attr('onclick', '');
		},
		success: function(obj){
			if(obj.status==1)
				location.href=location.href;
			else
				alert(obj.info);
		},
		error:function(){
			alert('error');
		}
	});
}

function mpp_del(id,cans,name){
	if(!id)
	{
		alert("请选择需要删除的信息");
		return;
	}
	if(cans){
		if(confirm("确定要删除该拟投项目关系吗？删除后不可恢复！"))
			$.ajax({ 
				url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=pp_delete&id="+id, 
				data: "ajax=1",
				dataType: "json",
				success: function(obj){
					if(obj.status==1)
						location.href=location.href;
					else
						alert(obj.info);
				}
			});
	}else{
		alert("基金【"+name+"】已经成立，不可以删除该项目！");
		return;
	}
}

// 去除sortBy的排序功能
function sortBy(){
	return false;
}

$(document).ready(function() {
 	$(".main .search_row").on("click","#mpp_button",function(e){
 		var name = $.trim($("#name").val());
 		if(name == ''){
 			return;
 		}
 		mpp_get_deal(name);
 	})
 	$(".main .search_row").on("click","#mpp_reset",function(e){
 		$("#name").val("");
 		mpp_get_deal();
 	})
 	$(".main .mpp_list").on("click",".page a",function(e){
 		e.preventDefault();
 		var href = $(this).attr("href").split('&');
// 		console.log(href);
 		var user_name = href[3].split('=');
 		var p = href[5].split('=');
 		mpp_get_deal(user_name[1],p[1]);
 	})
});