function pay(id)
{
	$.weeboxs.open(ROOT+'?m=Fund&a=pay&id='+id, {contentType:'ajax',showButton:false,title:'发放筹款',width:600,height:180});
}

function offline(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert("请选择需要下架的基金");
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");
	}
	if(confirm("确定要下架选中的基金吗？"))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=delete&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}

function fund_restore(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert("请选择需要恢复的基金");
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");
	}
	
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=fund_restore&id="+id, 
		data: "ajax=1",
		dataType: "json",
		success: function(obj){
			$("#info").html(obj.info);
			if(obj.status==1)
				location.href=location.href;
		}
	});
}

function fund_item(id)
{
	location.href = ROOT + '?m=Fund&a=fund_item&id='+id;
}

function pay_log(id)
{
	location.href = ROOT + '?m=Fund&a=pay_log&id='+id;
}

function fund_log(id)
{
	location.href = ROOT + '?m=Fund&a=fund_log&id='+id;
}

function fund_investor(id)
{
	location.href = ROOT + '?m=Fund&a=investor_list&fund_id='+id;
}

function fund_detail(id)
{
	location.href = ROOT + '?m=Fund&a=fund_detail&id='+id;
}

function investor_add(id)
{
	location.href = ROOT + '?m=Fund&a=investor_add&fund_id='+id;
}

function relation_add(id)
{
	location.href = ROOT + '?m=Fund&a=relation_add&fund_id='+id;
}

function investment_details(id)
{
	location.href = ROOT + '?m=Fund&a=relation_list&fund_id='+id;
}

function investor_edit(id)
{
	location.href = ROOT + '?m=Fund&a=investor_edit&id='+id;
}

function revoke(id)
{
	location.href = ROOT + '?m=Fund&a=update_fund_status&id='+id + '&status=' + 3;
}

function publish(id)
{
	location.href = ROOT + '?m=Fund&a=update_fund_status&id='+id+ '&status=' + 2;
}

function investor_del(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert(LANG['DELETE_EMPTY_WARNING']);
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");
	}
	if(confirm(LANG['CONFIRM_DELETE']))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=investor_del&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}

function relation_edit(id)
{
	location.href = ROOT + '?m=Fund&a=relation_edit&id='+id;
}

function relation_del(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert(LANG['DELETE_EMPTY_WARNING']);
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");
	}
	if(confirm(LANG['CONFIRM_DELETE']))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=relation_del&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}

function fund_preview(id)
{
 
	window.open('/index.php?ctl=funddetail&id='+id+'&preview=1');
}


function add_fund_item(fund_id)
{
	location.href = ROOT + '?m=Fund&a=add_fund_item&id='+fund_id;
}

function edit_fund_item(item_id)
{
	location.href = ROOT + '?m=Fund&a=edit_fund_item&id='+item_id;
}

function del_fund_item(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert("请选择需要删除的基金");
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");
	}
	if(confirm("确定要删除选中的基金吗？"))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=del_fund_item&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}

function add_pay_log(fund_id)
{
	$.weeboxs.open(ROOT+'?m=Fund&a=add_pay_log&id='+fund_id, {contentType:'ajax',showButton:false,title:"发放筹款",width:600,height:220});
}

function del_pay_log(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert("请选择需要删除的基金");
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");
	}
	if(confirm("确定要删除选中的基金吗？"))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=del_pay_log&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}

function del_fund_log(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert("请选择需要删除的基金");
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");
	}
	if(confirm("确定要删除选中的基金吗？"))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=Fund&"+VAR_ACTION+"=del_fund_log&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}

// 基金披露信息相关
function attachment_index(id){
	location.href = ROOT + '?m=Fund&a=attachment_index&fid='+id;
}
