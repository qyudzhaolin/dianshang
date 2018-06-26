function deal_item(id)
{
	location.href = ROOT + '?m=Deal&a=deal_item&id='+id;
}
 
function deal_preview(id)
{
	window.open('/index.php?ctl=dealdetails&id='+id+'&preview=1');
}
function add_deal_item(deal_id)
{
	location.href = ROOT + '?m=Deal&a=add_deal_item&id='+deal_id;
}

function deal_event(deal_id)
{
	location.href = ROOT + '?m=Deal&a=event_list&deal_id='+deal_id;
}
function deal_event_add(deal_id)
{
	location.href = ROOT + '?m=Deal&a=event_add&deal_id='+deal_id;
}
function deal_event_edit(id)
{
	location.href = ROOT + '?m=Deal&a=event_edit&id='+id;
}
function deal_see(id)
{
	location.href = ROOT + '?m=Deal&a=edit&deal_see=1&id='+id;
}
function deal_event_see(id)
{
	location.href = ROOT + '?m=Deal&a=deal_event_see&id='+id;
}
function deal_investor(id)
{
	location.href = ROOT + '?m=Deal&a=investor_list&event_id='+id;
}
function deal_investor_add(id)
{
	location.href = ROOT + '?m=Deal&a=investor_add&event_id='+id;
}
function deal_investor_edit(id)
{
	location.href = ROOT + '?m=Deal&a=investor_edit&id='+id;
}

function edit_deal_item(item_id)
{
	location.href = ROOT + '?m=Deal&a=edit_deal_item&id='+item_id;
}

function deal_trade_event_edit(id){
	location.href = ROOT + '?m=Deal&a=deal_trade_event_edit&id='+id;
}

function deal_trade_fund_edit(id){
	location.href = ROOT + '?m=Deal&a=deal_event_see&id='+id+'&edit=1';
}

function del_deal_item(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert("请选择需要删除的项目");
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");
	}
	if(confirm("确定要删除选中的项目吗？"))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=Deal&"+VAR_ACTION+"=del_deal_item&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}


function dealinted_edit(deal_id)
{
		location.href = ROOT + '?m=Deal&a=dealinted_edit&id='+deal_id;
}

function event_del(id)
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
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=event_del&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
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

function deal_intended(id)
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
	if(confirm('拟投项目上线后将不可编辑并且在前端显示，确定该拟投项目上线吗?'))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=deal_intended&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				alert('项目已上线！');
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}

function deal_completion(id)
{
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=deal_completion_check&id="+id, 
		data: "ajax=1",
		dataType: "json",
		success: function(obj){
			if(obj.status == 1){
				location.href = ROOT + '?m=Deal&a=deal_completion&id='+id;
			}else{
				alert(obj.info);
			}
		}
	});
}

function deal_withdraw(id)
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
	if(confirm('项目撤回后，在前端将不再显示，确定该撤回该项目吗?'))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=deal_withdraw&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				alert('撤回成功！');
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}

function deal_publish(id){
	if(!id){
		alert(LANG['DELETE_EMPTY_WARNING']);
		return;
	}
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=deal_publish&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				if(obj.status==1){
					alert('发布成功！');
					location.href=location.href;
				}else{
					alert(obj.info);
				}
			}
	});
}

function deal_new_finance(id){
	if(!id)
	{
		alert(LANG['DELETE_EMPTY_WARNING']);
		return;
	}
	if(confirm("确认为该项目申请新融资？"))
	$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=deal_new_finance&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				alert('申请新融资成功！');
				$("#info").html(obj.info);
				if(obj.status==1)
				location.href=location.href;
			}
	});
}