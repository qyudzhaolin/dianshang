//项目名称
function checkdeal_name(){
	if(trim(get_input_value('deal_name'))==''){
		$('#deal_name_msg').html('必填');
		$('#deal_name').addClass('warnning');
 
		$('#deal_name').focus();
		return false;
	}else{
		$('#deal_name_msg').html('');
		$('#deal_name').removeClass('warnning');
		return true;
	}
}
//项目一句话
function checkdeal_sign(){
	if(trim(get_input_value('deal_sign'))==''){
		$('#deal_sign_msg').html('必填');
		$('#deal_sign').addClass('warnning');
		$('#deal_sign').focus();
		return false;
	}else{
		$('#deal_sign_msg').html('');
		$('#deal_sign').removeClass('warnning');
		return true;
	}
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
//省和城市
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
//融资轮次
function checkdeal_period(){
	var t=$("input[name='deal_period']:checked").val(); 
	if(t==undefined||t==''){
		$('#deal_period_msg').html('必选');
		$('#deal_period').addClass('warnning');
		$('#deal_period').focus();
		return false;
	}else{
		$('#deal_period_msg').html('');
		$('#deal_period').removeClass('warnning');
		return true;
	}
}
//项目简介
function checkdeal_brief(){
	if(trim(get_input_value('deal_brief'))==''){
		$('#deal_brief_msg').html('必填');
		$('#deal_brief').addClass('warnning');
		$('#deal_brief').focus();
		return false;
	}else{
		$('#deal_brief_msg').html('');
		$('#deal_brief').removeClass('warnning');
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
function deal_save(){
	var query = new Object();    
	var region_check=checkRegion();
	var deal_name_check=checkdeal_name();
	var deal_sign_check=checkdeal_sign();
	var deal_cate_check=checkdeal_cate();
	var deal_period_check=checkdeal_period();
	var deal_brief_check=checkdeal_brief();
	var d =$("input[name=deal_cate]:checked");
	var value = '';
	 var chk_value=[];
    for(var i=0;i<d.size() ;i++)   
	{
	  	value=d[i].value+','+value;   
	}   
	query.deal_name =get_input_value('deal_name');
	query.deal_sign =get_input_value('deal_sign'); 
	query.deal_cate=value;
	query.province =get_input_value('province');
	query.city =get_input_value('city');
	var t=$("input[name='deal_period']:checked").val(); 
	query.deal_period =t;
	query.deal_brief =get_input_value('deal_brief');
	if ( !region_check||!deal_name_check ||!deal_sign_check ||!deal_cate_check ||! deal_period_check||!deal_brief_check  ) {return;}
	var ajaxTimeoutTest=$.ajax({ 
		url: APP_ROOT+"/index.php?ctl=ideal&act=deal_save",
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
			alert("推荐项目成功");
			  window.location.reload();
			}
           else if(ajaxobj.status==3)
			{  		
			alert("你已经推荐过该项目,不可重复推荐一个项目");
			  window.location.reload();
			}
			else
			{
			alert("推荐项目失败");
			}
		},
		error:function(ajaxobj)
		{
			$('#error_msg').html('提交失败');
		}
	});



}
