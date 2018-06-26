$(function(){
	$('.news_more.list_more').bind('click',function(){
		more('');//点击，调用查看更多
	});
	function more(serchtitle){//news_list 封装更多
		$('.news_more.list_more').attr('disabled',true).html("正在加载");
		var counter = serchtitle ? 0 : parseInt($('.news_content.new_active1 dl').size());
		var corner 	= parseInt($(".news_top_l .new_span_active").attr("rel"));
		$.ajax({
			type: "post",
			url: APP_ROOT + "/index.php?ctl=news&act=newslist_tab",
			data: "corner="+corner+"&counter="+counter+"&serchtitle="+serchtitle,
			dataType: "json",
			success: function(obj){
				var aEle = '';
				if(obj.status == 0){
					alert(obj.info);
				}else if(obj.status == 99 && serchtitle){
					aEle += '<dl class="news_content_list">';
					aEle += '<dd>';
					aEle += '<span class="font14-none">'+obj.info+'</span>';
					aEle += '</dd>';
					aEle += '</dl>';
				}else{
					$.each(obj.data,function(k,v){
						aEle += '<dl class="news_content_list">';
						aEle += '<dt class="bs">';
						aEle += '<a href="/news/'+v.id+'/" target="_blank"><img src="'+v.n_list_img+'" alt="'+v.n_title+'" width="136" height="136" /></a>';
						aEle += '</dt>';
						aEle += '<dd>';
						aEle += '<h2><a href="/news/'+v.id+'/" target="_blank" title="'+v.n_title+'">'+v.n_title+'</a></h2>';
						aEle += '<p class="p1">'+v.n_brief+'</p>';
						aEle += '<p class="p2">';
						aEle += '<span>来源：'+v.n_source+'</span> <span>'+v.create_time+'</span>';
						aEle += '</p>';
						aEle += '</dd>';
						aEle += '</dl>';
					});
				}
				
				// 查看更多按钮的显示隐藏状态切换
				if(obj.more){
					$('.news_more.list_more').show();
				}else{
					$('.news_more.list_more').hide();
				}
				
				counter > 0 ? $('.news_content.new_active1').append(aEle) : $('.news_content.new_active1').html(aEle);
				$('.news_more.list_more').html("查看更多").attr('disabled',false);
			}
		});
	}
	$('.search_index1').bind('click',function(){
		var serchtitle 	= $.trim(get_input_value("keyword"));
		if(serchtitle == '') return;
		$('.news_content.new_active1').html("");
		$('.news_more.list_more').html("正在加载").show();
		more(serchtitle);
	})
	
	$('#keyword').keydown(function(e){
        var curKey = e.which;
        if(curKey == 13){
        	$(".search_index1").click();
        	return false;
        }
    });
})