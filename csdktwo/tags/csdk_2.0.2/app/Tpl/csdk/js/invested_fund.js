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
							aEle += '<span>'+no+'</span>';
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
});
