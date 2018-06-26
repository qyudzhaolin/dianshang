var fund_team_index = 0;

function fund_team_search(o){
	var mid = parseInt($("#managers_id").val());
	if(mid == 0 || isNaN(mid)){
		$("html, body").animate({ scrollTop: 0 }, 120);
		$("#managers_id_msg").html("请选择基金管理人");
		return;
	}
	var height = $(window).height() - 95;
	$.weeboxs.open('<div id="ft_team_area"></div>', {
		title:'选择团队成员', 
		width:800,
		height:height,
		showButton:false,
		onclose:function(){
			
		}
	});
	fund_team_index = $(o).parent().index();
	var html = $("#ft_main").clone(true);
	html.find('.ft_team_list').attr('id','ft_team_list');
	html.find('input[name="ft_name"]').attr('id','ft_name');
	html.find('input[name="ft_title"]').attr('id','ft_title');
	html.attr('style','display:block');
	$("#ft_team_area").append(html);
	ft_get_user();
}

function ft_get_user(name,title,p){
	name = name || "";
	title = title || "";
	p = p || 1;
	var mid = parseInt($("#managers_id").val());
	$.ajax({
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=fund_add_team_search", 
		data: "mid="+mid+"&name="+name+"&title="+title+"&p="+p+"&ajax=1",
		dataType: "text",
		beforeSend: function () {
			$("#ft_team_list").html("loading...");
		},
		success: function(obj){
			$("#ft_team_list").html(obj);
		},
		error:function(){
			alert('error');
		}
	});
}

function ft_team_select(id){
	if(!id) return;
	var tmp = false;
	$.each($("#faq div"),function(k,v){
		var v = $(v).find("input[type=hidden]:eq(0)").val();
		console.log(v);
		if(id == v){
			tmp = true;
			alert('该团队成员已被选择过，请选择其它成员！');
			return false;
		}
	})
	if(tmp) return;
	var team_area = $(this).parent();
	$.ajax({
		url:ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=get_manage_team_info", 
		data: "id="+id+"&ajax=1",
		dataType:"json",
		success: function(obj){
			var obj_area = $("#faq div").eq(fund_team_index);
			obj_area.find('#img_src').attr('src',obj.data.real_logo);
			obj_area.find('input:eq(0)').val(obj.data.name);
			obj_area.find('input:eq(1)').val(obj.data.title);
			obj_area.find('input:eq(2)').val(obj.data.id);
			obj_area.find('input:eq(3)').val(obj.data.user_id);
			obj_area.find('textarea').val(obj.data.brief);
			$("body").trigger('click');
		},
		error:function(){
			alert('error');
		}
	});
}

//复选框互斥
function  is_director_check(obj){
	var size = $("#faq input[type=checkbox]:checked").size();
	if(size == 1){
		$("#faq input[type=checkbox]").prev().val("2");
		$(obj).prev().val("1");
		$("#faq input[type=checkbox]:not(:checked)").attr("disabled",true);
	}else if(size > 1){
		obj.checked = false;
		$("#faq input[type=checkbox]:not(:checked)").attr("disabled",true);
		alert("每支基金只能有一个主合伙人");
	}else{
		$("#faq input[type=checkbox]").prev().val("2");
		$("#faq input[type=checkbox]:not(:checked)").attr("disabled",false);
	}
}

$(document).ready(function() {
 	$(".main .search_row").on("click","#ft_team_button",function(e){
 		var name = $.trim($("#ft_name").val());
 		var title = $.trim($("#ft_title").val());
 		if(name == '' && title == ""){
 			return;
 		}
 		ft_get_user(name,title);
 	})
 	$(".main .search_row").on("click","#ft_team_reset",function(e){
 		$("#ft_name").val("");
 		$("#ft_title").val("");
 		ft_get_user();
 	})
 	$(".main .ft_team_list").on("click",".page a",function(e){
 		e.preventDefault();
 		// ["/m.php?m=Fund", "a=manageTeam_add_user_search", "mid=2", "name=", "title=", "ajax=1","managers_id=2","is_del=1","p=1"]
 		var href = $(this).attr("href").split('&');
 		var user_name = href[3].split('=');
 		var org_title = href[4].split('=');
 		var p = href[8].split('=');
 		ft_get_user(user_name[1],org_title[1],p[1]);
 	})
 	$("#managers_id").change(function(){
 		$("#faq div a").trigger("click");
 	});
});