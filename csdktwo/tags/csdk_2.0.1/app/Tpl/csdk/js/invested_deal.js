$(function(){
    //下三角控制
    $('.information_box .bar').css({'left':59});
    $('.information_box li').bind('click',function(){
        var this_index = $(this).index();
        $('.information_box ul li').removeClass('active');
        $('.information_box ul li').eq(this_index).addClass('active');
        $('.information_box .bar').css({'left':(($(this).outerWidth()*this_index)+$(this).outerWidth()/2)-6})
    })
    //新闻动态 切换
    $('.class_ifi_cation').on('click','.pad62_infi h2 span',function(){
        var this_index = $(this).index();
        $('.pad62_infi h2 span').removeClass('active');
        $('.pad62_infi h2 span').eq(this_index).addClass('active');
        $('.pad62_ul').removeClass('active');
        $('.pad62_ul').eq(this_index).addClass('active');
    });
    //融资信息 切换
    $('.information_box ul li').bind('click',function(){
        var this_index = $(this).index();
        $('.class_ifi_cation').removeClass('active');
        $('.class_ifi_cation').eq(this_index).addClass('active');
        if($.inArray(this_index,[3,4]) >= 0){
        	if($('.class_ifi_cation:eq('+this_index+')').html()) return;
        	more(this_index);
        }
    });

    //饼图切换
    $('.ol_padding76 li').bind('click',function(){
        var this_index = $(this).index();
        $('.Map_close ul li').css({'zIndex':'1'});

        $('.Map_close ul li').eq(this_index).css({'zIndex':'222'});
        $('.ol_padding76 li a').removeClass('active');
        $('.ol_padding76 li a').eq(this_index).addClass('active');
    });

    function more(t_index){
    	$('.class_ifi_cation:eq('+t_index+')').html("loading...");
    	var deal_id = parseInt($(".information_box ul li:eq(3)").attr("data-id"));
    	var flag = $.trim($(".information_box ul li:eq("+t_index+")").attr("data-type"));
    	$.ajax({
	 		type: "post",
			url: APP_ROOT + "/index.php?ctl=invested&act=deal_more",
			data: "deal_id="+deal_id+"&flag="+flag,
			dataType: "json",
			success: function(obj){
				var aEle = '';
				if(obj.status == 0){
					// error
					aEle += '';
				}else if(obj.status == 1){
					$.each(obj.info,function(k,v){
						aEle += '<div class="pad59_infi">';
						aEle += '<ul class="centent_margin bs character clearfix">';
						aEle += '<li></li>';
						aEle += '<li><dl>';
						aEle += '<dt>'+v.name+'<span>（'+v.title+'）</span></dt><dd>'+v.brief+'</dd>';
						aEle += '</dl></li>';
						aEle += '</ul>';
						aEle += '</div>';
					});
				}else if(obj.status == 33){
					aEle += '<div class="capital_infi"><span class="font14-none">暂无相关资讯</span></div>';
				}else if(obj.status == 2 || obj.status == 22 || obj.status == 44){
					aEle += '<div class="pad62_infi">';
					aEle += '<h2><span class="active">公司新闻</span><span>&nbsp;行业新闻</span></h2>';
					aEle += '<ul class="pad62_ul active">';
					if(obj.status == 22){
						aEle += '<span class="font14-none">暂无相关资讯</span>';
					}else{
						$.each(obj.info.news,function(k,v){
							aEle += '<li>';
							aEle += '<span><a href="'+v.url+'" target="_blank">'+v.n_title+'</a></span>';
							aEle += '<span style="text-align:right">'+v.create_time+'</span>';
							aEle += '</li>';
						});
					}
					aEle += '</ul>';
					aEle += '<ul class="pad62_ul">';
					if(obj.status == 44){
						aEle += '<span class="font14-none">暂无相关资讯</span>';
					}else{
						$.each(obj.info.news_cate,function(k,v){
							aEle += '<li>';
							aEle += '<span><a href="'+v.url+'" target="_blank">'+v.n_title+'</a></span>';
							aEle += '<span style="text-align:right">'+v.create_time+'</span>';
							aEle += '</li>';
						});
					}
					aEle += '</ul>';
					aEle += '</div>';
				}else{
					aEle += obj.info;
				}
				$('.class_ifi_cation:eq('+t_index+')').html(aEle);
			}
	 	});
    };
});