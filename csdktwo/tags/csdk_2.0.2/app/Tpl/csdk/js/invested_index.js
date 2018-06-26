$(function(){
	// 查看更多
	$(".news_more_box .fund_more").click(function(){
		var counter = parseInt($(".fund_ul li").size());
		var keyword = $.trim(get_input_value("keyword"));
		more(counter,keyword,'btn');
	});
	
	// 搜索
	$("#invested_search").click(function(){
		var keyword = $.trim(get_input_value("keyword"));
		if(keyword == '') return;
		$(this).attr('disabled',true);
		$('.fund_ul').html("");
		$(".news_more_box .fund_more").show();
		more(0,keyword,'search');
	});
	
	// 调用数据接口方法
	function more(counter,keyword,field){
		$(".news_more_box .fund_more").attr('disabled',true).html("正在加载");
		$.ajax({
	 		type: "post",
			url: APP_ROOT + "/index.php?ctl=invested&act=my_invested_more",
			data: "counter="+counter+"&keyword="+keyword,
			dataType: "json",
			success: function(obj){
				var aEle = '';
				if(obj.status == 0){
					// error
					aEle += '';
				}else{
					if(obj.status == 1 && field == 'search'){
						aEle += '<li class="no_deal">'+obj.info+'</li>';
					}else{
						var no = parseInt($(".fund_ul li:last span:first").html());
						no = isNaN(no) ? 0 : no;
						 
						$.each(obj.data,function(k,v){
							no++;
							aEle += '<li>';
							aEle += '<span>'+no+'</span>';
							aEle += '<span><a href="'+v.fund_url+'" target="_blank">'+v.short_name+'</a></span>';
							aEle += '<span>'+v.name+'</span>';
							aEle += '<span>'+v.total_amount+'万</span>';
							aEle += '<span>'+v.investor_amount+'万</span>';
							aEle += '<span>'+v.establish_date+'</span>';
							aEle += '</li>';
						});
					}
				}
				$('.fund_ul').append(aEle);
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
	
	$('#keyword').keydown(function(e){
        var curKey = e.which;
        if(curKey == 13){
        	$("#invested_search").click();
        	return false;
        }
    });
});
