$(function(){
	// 查看更多
	$(".System_btn .index_more").click(function(){
		var counter = parseInt($(".System_box li").size());
		more(counter);
	});
	
	// 查看更多-调用数据接口方法
	function more(counter){
		$(".System_btn .index_more").attr('disabled',true).html("正在加载");
		$.ajax({
	 		type: "post",
				url: APP_ROOT + "/index.php?ctl=message&act=more",
				data: "counter="+counter,
				dataType: "json",
				success: function(obj){
					var aEle = '';
					if(obj.status == 0){
						// error
						aEle += '';
					}else{
						$.each(obj.data,function(k,v){
							aEle += '<li>';
							aEle += '<span>系统通知：</span>';
							aEle += '<span>'+v.log_time+'</span>';
							aEle += '<p>'+v.log_info+'</p>';
							aEle += '</li>';
						});
					}
					$(".System_box").append(aEle);
		            // 查看更多按钮的显示隐藏状态切换
					if(obj.info == 1){
						$(".System_btn .index_more").show();
					}else{
						$(".System_btn .index_more").hide();
					}
					$(".System_btn .index_more").attr('disabled',false).html("显示更多");
				}
	 	})
	}
});
