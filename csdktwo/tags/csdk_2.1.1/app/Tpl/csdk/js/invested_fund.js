$(function(){
	// 查看更多
	$(".news_more_box .fund_more").click(function(){
		more();
	});
	
	// 调用数据接口方法
	function more(){
		var id = parseInt($(".news_more_box .fund_more").attr("data-id"));
		var counter = parseInt($(".fund_ul li").size()) - 1;
		$(".news_more_box .fund_more").attr('disabled',true).html("正在加载");
		$.ajax({
	 		type: "post",
				url: APP_ROOT + "/index.php?ctl=invested&act=invested_fund_more",
				data: "id="+id+"&counter="+counter,
				dataType: "json",
				success: function(obj){
					var aEle = '';
					if(obj.status == 0){
						// error
						aEle += '';
					}else{
						var no = parseInt($('.fund_ul li:last').prev().find("span:first").html());
						$.each(obj.data,function(k,v){
							no++;
							aEle += '<li>';
							 
							aEle += '<span><a href="'+v.fund_url+'">'+v.s_name+'</a></span>';
							aEle += '<span>'+v.name+'</span>';
							aEle += '<span>'+v.investor_amount+'万</span>';
							aEle += '<span>'+v.valuations+'万</span>';
							aEle += '<span>'+v.investor_date+'</span>';
							aEle += '</li>';
						});
					}
					$('.fund_ul li:last').before(aEle);
		            // 查看更多按钮的显示隐藏状态切换
					if(obj.more){
						$(".news_more_box .fund_more").show();
					}else{
						$(".news_more_box .fund_more").hide();
					}
					$(".news_more_box .fund_more").attr('disabled',false).html("显示更多");
				}
	 	})
	}
	
	// 披露查看更多
	$(".news_more_box .attachment_more").click(function(){
		var id = parseInt($(this).attr("data-id"));
		var counter = parseInt($(this).parent().prevAll().size());
		$(this).attr('disabled',true).html("正在加载");
		console.log(id);
		console.log(counter);
		$.ajax({
	 		type: "post",
				url: APP_ROOT + "/index.php?ctl=invested&act=attachment_more",
				data: "id="+id+"&counter="+counter,
				dataType: "json",
				success: function(obj){
					var aEle = '';
					if(obj.status == 0){
						// error
						aEle += '';
					}else{
						$.each(obj.data,function(k,v){
							aEle += '<p class="clearfix">';
							aEle += '<img src="'+v.typeSrc+'" alt="" class="fl" style="margin:12px 20px 0 0"/><a href="'+v.href+'" class="fl" target= "_blank">'+v.title+'</a>';
							aEle += '</p>';
						});
					}
					$('#attachment_list .news_more_box').before(aEle);
		            // 查看更多按钮的显示隐藏状态切换
					if(obj.more){
						$(".news_more_box .attachment_more").show();
					}else{
						$(".news_more_box .attachment_more").hide();
					}
					$(".news_more_box .attachment_more").attr('disabled',false).html("显示更多");
				}
	 	})
	});
});
