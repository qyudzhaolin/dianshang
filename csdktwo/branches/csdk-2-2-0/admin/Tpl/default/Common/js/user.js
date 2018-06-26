function account(user_id)
{
	$.weeboxs.open(ROOT+'?m=User&a=account&id='+user_id, {contentType:'ajax',showButton:false,title:LANG['USER_ACCOUNT'],width:600,height:180});
}
function account_detail(user_id)
{
	location.href = ROOT + '?m=User&a=account_detail&id='+user_id;
}

function consignee(user_id)
{
	location.href = ROOT + '?m=User&a=consignee&id='+user_id;
}

function edit_user_pwd(user_id,from)
{
	location.href = ROOT + '?m=User&a=edit_user_pwd&id='+user_id+'&from='+from;
}

function user_detail(user_id)
{
	location.href = ROOT + '?m=User&a=user_detail&id='+user_id;
}

function weibo(user_id)
{
	location.href = ROOT + '?m=User&a=weibo&id='+user_id;
}

function user_del(user_id){
	var content = '<textarea id="delete_reason" style="margin: 0px; height: 90px; width: 280px;"></textarea>';
	$.weeboxs.open(content, {
		title:'删除理由', 
		width:300, 
		height:100,
		onok:function(){
			var id = parseInt(user_id);
			var reason = $.trim($("#delete_reason").val());
			if(id == 0 || isNaN(id) || reason == ''){
				alert('请输入删除原因！');
				return;
			}
			
			$.ajax({
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=delete", 
				data: "id="+id+"&reason="+reason+"&ajax=1",
				dataType: "json",
				success: function(obj){
					if(obj.status == 0){
						alert(obj.info);
					}else{
						location.href=location.href;
					}
				},
				error:function(e){
					alert('error');
				}
			});
		}
	});
}

function user_restore(user_id){
	$.weeboxs.open('<div style="margin:0 auto;text-align: center;line-height: 100px;">点击“是”会跳转至重置密码页面</div>', {
		title:'是否需要重置密码', 
		width:300, 
		height:100,
		okBtnName:'是',
		cancelBtnName:'否',
		onok:function(){
			do_user_restore(user_id,1);
		},
		oncancel:function(){
			do_user_restore(user_id,0);
		}
	});
}

function do_user_restore(user_id,flag){
	var id = parseInt(user_id);
	if(id == 0 || isNaN(id)){
		alert('无法获取用户ID！');
		return;
	}
	
	$.ajax({
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=restore&id="+id, 
		data: "ajax=1",
		dataType: "json",
		success: function(obj){
			if(flag){
				edit_user_pwd(user_id,1);
			}else{
				location.href=location.href;
			}
		}
	});
}
