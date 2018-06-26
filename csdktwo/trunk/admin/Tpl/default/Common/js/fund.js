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
	location.href = ROOT + '?m=Fund&a=investorintention_list&fund_id='+id;
}
function fund_manage_investor(id)
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

// 基金撤回
function revoke(id)
{
	$.ajax({ 
		url: ROOT + '?m=Fund&a=update_fund_status&id='+id + '&status=3',
		data: "ajax=1",
		dataType: "json",
		success: function(obj){
			if(obj.status==1){
				location.href=location.href;
			}else{
				alert(obj.info);
			}
		}
	});
}

// 管理基金拟投项目
function manage_proposed_project(id){
	location.href = ROOT + '?m=Fund&a=manage_proposed_project&id='+id;
}

// 基金发布
function publish(id)
{
	if(confirm("确定要发布么？"))
		$.ajax({ 
				url: ROOT + '?m=Fund&a=update_fund_status&id='+id+ '&status=2', 
				data: "ajax=1",
				dataType: "json",
				success: function(obj){
					if(obj.status==1){
						location.href=location.href;
					}else{
						alert(obj.info);
					}
				}
		});
}
//意向状态通过处理
function intention_pass(id)
{
	location.href = ROOT + '?m=Fund&a=intention_pass&id='+id;
}
//意向股份确认
function intention_confirm(id)
{
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=intention_confirm_check&id="+id, 
		data: "ajax=1",
		dataType: "json",
		success: function(obj){
			$("#info").html(obj.info);
			if(obj.status==1){
				if(confirm("份额确定后不可修改，确定意向投资人["+obj.info.name+"]的实际认购份额为["+obj.info.actual_invest_amount+"]万元吗？")){
					if(obj.info.investor > 0){
						$.weeboxs.open('<div style="margin:0 auto;text-align: center;padding: 30px 0">点击“是”只更新认购金额，点击“否”不做任何操作！</div>', {
							title:'该意向投资人已在投资人列表中存在，是否覆盖原来的投资人数据？', 
							width:485, 
							height:100,
							okBtnName:'是',
							cancelBtnName:'否',
							onok:function(){
								location.href = ROOT + '?m=Fund&a=intention_confirm&id='+id+'&update=1';
							},
							oncancel:function(){
								location.href = ROOT + '?m=Fund&a=intention_confirm&id='+id+'&update=0';
							}
						});
					}else{
						location.href = ROOT + '?m=Fund&a=intention_confirm&id='+id;
					}
				}
	        }else{
	        	alert(obj.info);
	        }
		}
	});
}
//意向驳回状态处理
function intention_rejected(id)
{        
	if(confirm("确定要审核驳回吗？"))
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=intention_rejected_check&id="+id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				if(obj.status==1){
			    $("#info").html(obj.info);
				location.href=ROOT + '?m=Fund&a=intention_rejected&id='+obj.info.id;
		        }else{
				alert(obj.info);
			}
			}
	});
}
// 基金成立
function do_fund_establishment(id,total_amount){
	
	$.ajax({
		url: ROOT + '?m=Fund&a=fund_establishment_check&id='+id, 
		data: "ajax=1",
		dataType: "json",
		success: function(obj){
			if(obj.status==1){
				if(confirm("该基金规模为"+total_amount+"万元，正式成立后，募集金额及意向投资人数据均不可更改，确定要正式成立吗？")){
					$.ajax({ 
						url: ROOT + '?m=Fund&a=fund_establishment&id='+id, 
						data: "ajax=1",
						dataType: "json",
						success: function(obj){
							if(obj.status==1){
								if(confirm("修改成功！是否跳转至 【基金管理阶段信息】 编辑页面？")){
									edit(id);
								}else{
									location.href=location.href;
								}
							}else{
								alert(obj.info);
							}
						}
					});
				}
			}else{
				alert(obj.info);
			}
		}
	});
}
function fund_establishment(id,total_amount,actual_invest_confirm){
	if(actual_invest_confirm){
		$.weeboxs.open('<div style="margin:0 auto;text-align: center;padding: 30px 0">点击“是”会跳转至意向投资人管理界面，点击“否”继续进行基金成立操作！</div>', {
			title:'该基金尚有未进行【股份确认】的意向投资人数据，是否处理？', 
			width:445, 
			height:100,
			okBtnName:'是',
			cancelBtnName:'否',
			onok:function(){
				fund_investor(id);
			},
			oncancel:function(){
				$("body .dialog-close").trigger('click');
				do_fund_establishment(id,total_amount);
			}
		});
	}else{
		do_fund_establishment(id,total_amount);
	}
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

// 意向投资人列表->修改实际认购金额
function set_actual_invest_amount(id,actual_invest_amount,invest_min_amount,domobj)
{
	$(domobj).html("").after("<input type='text' value='"+actual_invest_amount+"' id='set_actual_invest_amount' class='textbox5 require' maxlength='7' own_type='number' />");
	$("#set_actual_invest_amount").select();
	$("#set_actual_invest_amount").focus();
	$("#set_actual_invest_amount").bind("blur",function(){
		var newamount = parseInt($(this).val());
		var defaultValue = document.getElementById('set_actual_invest_amount').defaultValue;
		if(newamount == defaultValue){
			// 值没变化，不往下执行
			$(domobj).next().remove();
			$(domobj).html(actual_invest_amount);
			return;
		}
		if(newamount < invest_min_amount || newamount > 9990000){
			$(domobj).next().remove();
			$(domobj).html(actual_invest_amount);
			alert(newamount+" 不满足条件： "+invest_min_amount+"万 <= 实际认购金额 <= 999亿");
			return;
		}
		$.ajax({
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=set_actual_invest_amount&id="+id+"&actual_invest_amount="+newamount, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status)
				{
					$(domobj).next().remove();
					$(domobj).html(newamount);
					location.href=location.href;
				}
				else
				{
					alert(obj.info);
				}
			}
		});
	});
}

// 意向投资人列表->修改备注
function set_actual_invest_remark(id,actual_invest_remark,domobj)
{
	$(domobj).html("").after("<textarea id='set_actual_invest_remark' class='require' maxlength='100'>"+actual_invest_remark+"</textarea>");
	$("#set_actual_invest_remark").select();
	$("#set_actual_invest_remark").focus();
	$("#set_actual_invest_remark").bind("blur",function(){
		var newremark = $.trim($(this).val());
		var defaultValue = document.getElementById('set_actual_invest_remark').defaultValue;
		if(newremark == defaultValue){
			// 值没变化，不往下执行
			$(domobj).next().remove();
			$(domobj).html(actual_invest_remark);
			return;
		}
		if(newremark == "" || newremark.length > 100){
			$(domobj).next().remove();
			$(domobj).html(actual_invest_remark);
			alert("请输入备注信息并确保在100个字以内");
		}else{
			$.ajax({ 
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=set_actual_invest_remark&id="+id+"&actual_invest_remark="+newremark, 
				data: "ajax=1",
				dataType: "json",
				success: function(obj){
					$("#info").html(obj.info);
					if(obj.status)
					{
						$(domobj).next().remove();
						$(domobj).html(newremark);
						location.href=location.href;
					}
					else
					{
						alert(obj.info);
					}
				}
			});
		}
	});
}

// 投资人列表->批量计算占比
function investor_batch_accounting(fund_id,batch_fund_period,batch_expectant_investor){
	if(batch_fund_period){
		alert("该基金未正式成立不可以批量计算占比，请先进行基金成立操作！");
	}else{
		if(batch_expectant_investor){
//			if(confirm("该基金尚存在未进行认购份额确认的准投资人，确认要进行投资份额占比计算吗？")){
			if(confirm("确认要进行投资份额占比计算吗？")){
				do_investor_batch_accounting(fund_id);
			}
		}else{
			do_investor_batch_accounting(fund_id);
		}
	}
	
}
function do_investor_batch_accounting(fund_id){
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=investor_batch_accounting&id="+fund_id, 
		data: "ajax=1",
		dataType: "json",
		success: function(obj){
			$("#info").html(obj.info);
			alert(obj.info);
			if(obj.status)
			{
				location.href = location.href;
			}
		}
	});
}