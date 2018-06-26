$(function(){
	// 查看更多
	$(".invest_btn .index_more").click(function(){
		var counter = parseInt($(".invest_box_I_want_to .invest_I_want_to").size());
		more(counter);
	});
	
	// 查看更多-调用数据接口方法
	function more(counter){
		$(".invest_btn .index_more").attr('disabled',true).html("正在加载");
		$.ajax({
	 		type: "post",
				url: APP_ROOT + "/index.php?ctl=investing&act=more",
				data: "counter="+counter,
				dataType: "json",
				success: function(obj){
					var aEle = '';
					if(obj.status == 0){
						// error
						aEle += '';
					}else{
						$.each(obj.data,function(k,v){
							aEle += '<div class="invest_I_want_to clearfix">';
							aEle += '<a href="'+v.url+'" target="_blank"><img src="'+v.img_deal_logo+'" alt="" width="100" height="100" /></a>';
							aEle += '<div>';
							aEle += '<dl>';
							aEle += '<dt><a href="'+v.url+'" target="_blank">'+v.name+'</a></dt>';
							aEle += '<dd>'+v.deal_sign+'</dd>';
							aEle += '</dl>';
							aEle += '<p>';
							aEle += '<span>'+v.province+' </span>';
							aEle += '<span>'+v.period_id+'</span>';
							if(v.focus){
								aEle += '<span class="active">';
							}else{
								aEle += '<span id="focus_'+v.deal_id+'" data-count="'+v.comment_count+'" onclick="do_focus('+v.deal_id+',\'focus_'+v.deal_id+'\')">';
							}
							aEle += '关注&nbsp;'+v.comment_count+'</span>';
							aEle += '</p>';
							aEle += '</div>';
							aEle += '</div>';
						});
					}
					$(".invest_btn").before(aEle);
		            // 查看更多按钮的显示隐藏状态切换
					if(obj.more){
						$(".invest_btn .index_more").show();
					}else{
						$(".invest_btn .index_more").hide();
					}
					$(".invest_btn .index_more").attr('disabled',false).html("显示更多");
				}
	 	})
	}
});
