 
function checkLogo(obj1,obj2,obj3){
	if(get_input_value(obj1)==''){
		$('#'+obj1+'_msg').removeClass('gray');
		$('#'+obj1+'_msg').html('请添加头像');
		$('#'+obj2+'').addClass('logored');
		$('#'+obj3+'').focus();
		return false;
	}else{
		$('#'+obj1+'_msg').html('点击图片重新上传');
		$('#'+obj2+'').removeClass('logored');
		return true;
	}

}

//验证邮箱的格式
function checkEmail(){
    var patt= /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    //var deal_url=trim(document.getElementById(id).value);
    var email=trim(get_input_value('email'));
        if(patt.test(email) || email==""){
            $('#email_msg').html('');
            return true;
        }else{
            $('#email_msg').html('邮箱格式不正确');
            $('#email').addClass('warnning');
            $('#email').focus();
            return false;          
        }
}

function checkRegion(){
	if(trim(get_input_value('province'))==''||trim(get_input_value('city'))==''){
		$('#region_msg').html('必填');
		$('#province').addClass('warnning');
		$('#city').addClass('warnning');
		$('#province').focus();
		return false;
	}else{

		$('#region_msg').html('');
		$('#province').removeClass('warnning');
		$('#city').removeClass('warnning');
		return true;
	}
}
 
function checkDegree(){
	if(trim(get_input_value('degree'))==''){
		$('#degree_msg').html('必填');
		$('#degree').addClass('warnning');
		$('#degree').focus();
		return false;
	}else{
		$('#degree_msg').html('');
		$('#degree').removeClass('warnning');
		return true;
	}
}
  //行业选择限制
function  doCheck(obj,limit,my_id){
	var iCount=0;
	var c=$("input[tag="+my_id+"]").size();
	for(var i=0;i<c;i++)   
	{   
	  	var chk = document.getElementById(my_id+i);   
	  	if(chk.checked){
	  		iCount++;  
	  	}   
	}
	if(iCount>limit){
	 obj.checked=false,c--;
	 alert("你已经选满"+limit+"条了。")
	}
}

function personal_msg_next(){
	var personal_msg_json='[{"name":"email","max_len":30,"error_id":"email_msg","error_msg":"请确保内容在30个字以内"}]';
	var res=csdk_check(personal_msg_json);
	var region_check=checkRegion();
	var degree_check=checkDegree();
	var img_check=checkLogo('img_user_logo','personal_logo','user_img');
    var email_check = checkEmail();
	if (!img_check || !region_check|| !degree_check || !email_check || !res) {return;}
	var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=personal_update";
	var query = new Object();    
	query.img_user_logo =get_input_value('img_user_logo');
	query.province =get_input_value('province'); 
	query.city =get_input_value('city');
	query.degree =get_input_value('degree');
	query.email =get_input_value('email');
	var ajaxTimeoutTest=$.ajax({ 
		url: ajaxurl,
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$(".next").attr('disabled', 'disabled');
			$("#error_msg").html('提交中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status=='timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#error_msg").html('提交失败');
			}
			$(".next").removeAttr('disabled');
		},
		success: function(ajaxobj){
			if(ajaxobj.status==1)
			{  		//alert(document.getElementById("img_src").src);
					alert("个人资料更新成功");
					
					window.location.href = APP_ROOT+"/home/";
					 
			}
			else
			{
				$('#error_msg').html(ajaxobj.info);
			}
		},
		error:function(ajaxobj)
		{
			$('#error_msg').html('提交失败');
		}
	});

}
//所属行业
function checkdeal_cate(){
		var size =$("input[name=deal_cate]:checked").size();
	if(size==''){
		$('#deal_cate_msg').html('必选');
		$('#deal_cate').addClass('warnning');
		$('#deal_cate').focus();
		return false;
	}else{
		$('#deal_cate_msg').html('');
		$('#deal_cate').removeClass('warnning');
		return true;
	}
}
 function deal_cate_save(){
 	var query = new Object();
 	var deal_cate_check=checkdeal_cate();   
	var d =$("input[name=deal_cate]:checked");
	var value = '';
	var chk_value=[];
    for(var i=0;i<d.size() ;i++)   
	{
	  	value=d[i].value+','+value;   
	}   
	query.deal_cate=value;
   if ( !checkdeal_cate ) {return;}
	var ajaxTimeoutTest=$.ajax({ 
		url: APP_ROOT+"/index.php?ctl=home&act=deal_cate_save",
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$(".save").attr('disabled', 'disabled');
			$("#error_msg").html('提交中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status=='timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#error_msg").html('提交失败');
			}
			$(".save").removeAttr('disabled');
		},
		success: function(ajaxobj){
			if(ajaxobj.status==1)
			{  		
			alert("保存成功");
			}
			else
			{
			alert("保存失败");
			}
		},
		error:function(ajaxobj)
		{
			$('#error_msg').html('提交失败');
		}
	});

 }
//在线网址
function checkOrgUrl(){
	var patt = /^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/;
 
	var org_url=trim(get_input_value('org_url'));
 
	if(patt.test(org_url) || org_url==""){
		$('#org_url_msg').html('');
		$('#org_url').removeClass('warnning');
		return true;
	}else{
		$('#org_url_msg').html('网址格式不正确');
		$('#org_url').addClass('warnning');
		$('#org_url').focus();
		return false;          
	}

}

//公司联系电话
function checkOrgTel(){
	var patt =/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$|(^(13[0-9]|15[0|3|6|7|8|9]|18[8|9])\d{8}$)/;
	var org_mobile=trim(get_input_value('org_mobile'));
 
	if(patt.test(org_mobile) || org_mobile==""){
		$('#org_mobile_msg').html('');
		$('#org_mobile').removeClass('warnning');
		return true;
	}else{
		$('#org_mobile_msg').html('联系电话格式不正确1');
		$('#org_mobile').addClass('warnning');
		$('#org_mobile').focus();
		return false;          
	}

}
function investor_org_next(){  
	var organization_json='[{"name":"org_title","required":true,"error_id":"org_title_msg","error_msg":"请填写担任职务"},\
		{"name":"org_title","max_len":8,"required":true,"error_id":"org_title_msg","error_msg":"请确保担任职务在8个字以内"},\
		{"name":"org_name","required":true,"error_id":"org_name_msg","error_msg":"请填写所属公司名称"},\
		{"name":"org_name","max_len":20,"required":true,"error_id":"org_name_msg","error_msg":"请确保公司名称在20个字以内"}]';
	var res=csdk_check(organization_json);
	var url_check=checkOrgUrl();
	//var tel_check=checkOrgTel();
	if ( !url_check   || !res ) {return;}
	var ajaxurl = APP_ROOT+"/index.php?ctl=home&act=investor_org_update";

	var query = new Object();    
	query.org_name =get_input_value('org_name');
	query.org_desc =get_input_value('org_desc');
	query.org_mobile =get_input_value('org_mobile');
	query.org_title =get_input_value('org_title');
	query.org_linkman =get_input_value('org_linkman');
	query.org_url =get_input_value('org_url');
	query.user_id =get_input_value('user_id');

	var ajaxTimeoutTest=$.ajax({ 
		url: ajaxurl,
		timeout:20000,
		dataType: "json",
		data:query,
		type: "POST",
		beforeSend: function () {
			$(".next").attr('disabled', 'disabled');
			//alert("公司信息保存成功");
			$("#error_msg").html('提交中...');
		},
		complete: function (XMLHttpRequest,status) {
			if(status=='timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				$("#error_msg").html('提交失败');
			}
			$(".next").removeAttr('disabled');
		},
		success: function(ajaxobj){
			if(ajaxobj.status==1)
			{   
				$('#org_name').removeClass('warnning');
				$('#org_title').removeClass('warnning');
				$('#org_name_rw').val(query.org_name);
				$('#org_title_rw').val(query.org_title);
				alert("公司信息更新成功");
				//window.location.href = APP_ROOT+"/home/investor_org";
				
			}
			else
			{
				$('#error_msg').html(ajaxobj.info);
			}
		},
		error:function(ajaxobj)
		{
			$('#error_msg').html('提交失败');  
		}
	});

}
 
