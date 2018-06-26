$(function(){
    // 首页资讯tab切换
    $('.div1 div').css({'width':$('.div1 a').eq(0).innerWidth()});
    $('.div1 a').bind('click',function(){
        var index_i = $(this).index();
        var oAi = $('.div1 a').eq(index_i).innerWidth();
        $('.div1 div').stop().animate({'width':oAi});
        $('.div1 a').removeClass('all_options');
        $('.div1 a').eq(index_i).addClass('all_options');
        $('.text_infi_content').removeClass('all_active');
        $('.text_infi_content').eq(index_i).addClass('text_infi_content all_active');
        if(index_i == 3){
            $('.div1 div').stop().animate({'left':(oAi*index_i)-110,'width':'132'})
        }else{
            $('.div1 div').stop().animate({'left':oAi*index_i},400);
            $('.div1 div').animate({'width':'96'})
        }
        
        // about data
        if($('.text_infi_content').eq($(this).index()).html()){
        	$(this).attr("data-type") == 1 ? $('.news_more_box').show() : $('.news_more_box').hide();
        	return;
        }else{
        	$('.news_more_box').show();
        }
        more_index($(this).attr("rel"),0,$(this).index());
    });
    
    // 右侧边栏去除最后一行border
    $('.gengduo .investors_location .online_box:last').css({'borderBottom':'transparent'});
    $('.gengduo .investors_location2 .online_box:last').css({'borderBottom':'transparent'});

    // 左侧边栏new_list选项
    $('.news_top_l span').click(function(){
        var this_01 = $(this).index();
        $('.news_top_l span').removeClass('new_span_active');
        $('.news_top_l span').eq(this_01).addClass('new_span_active');
        $('.news_content').removeClass('new_active1');
        $('.news_content').eq(this_01).addClass('new_active1');
    });

    // 首页点击更多按钮
    $('.news_more.index_more').bind('click',function(){
        var index = $(".div1 a.all_options").index();
        var corner = parseInt($(".div1 a.all_options").attr("rel"));
        var counter = parseInt($('.text_infi_content:eq('+index+') dl').size());
        more_index(corner,counter,index);//index.html 更多调用
    });
    
    //首页资讯更多信息加载接口
 	function more_index(corner,counter,index){
	 	$('.news_more.index_more').attr('disabled',true).html("正在加载");
	 	$.ajax({
	 		type: "post",
				url: APP_ROOT + "/index.php?ctl=index&act=news_tab",
				data: "corner="+corner+"&counter="+counter,
				dataType: "json",
				success: function(obj){
					var aEle = '';
					if(obj.status == 0){
						alert(obj.info);
					}else if(obj.status == 99){
						aEle += '<dl class="news_content_list">';
						aEle += '<dd><span class="font14-none">'+obj.info+'</span></dd>';
						aEle += '</dl>';
					}else{
						$.each(obj.data,function(k,v){
							aEle += '<dl class="news_content_list">';
							aEle += '<dt class="bs">';
							aEle += '<a href="'+v.url+'" target="_blank"><img src="'+v.n_list_img+'" alt="'+v.n_title+'" width="136" height="136" /></a>';
							aEle += '</dt>';
							aEle += '<dd>';
							aEle += '<h2><a href="'+v.url+'" target="_blank"  title="'+v.n_title+'">'+v.n_title+'</a></h2>';
							aEle += '<p class="p1">'+v.n_brief+'</p>';
							aEle += '<p class="p2">';
							aEle += '<span>来源：'+v.n_source+'</span> <span>'+v.create_time+'</span>';
							aEle += '</p>';
							aEle += '</dd>';
							aEle += '</dl>';
						});
					}
					counter > 0 ? $('.text_infi_content:eq('+index+')').append(aEle) : $('.text_infi_content:eq('+index+')').html(aEle);
		            // 查看更多按钮的显示隐藏状态切换
					if(obj.more){
						$('.news_more_box').show();
					}else{
						// tab切换更多按钮的显示状态标识赋值
						$('.div1 a').eq(index).attr("data-type","0");
						$('.news_more_box').hide();
					}
					$('.news_more.index_more').attr('disabled',false).html("显示更多");
				}
	 	})
	 };

	var swiper2 = new Swiper('.swiper-container2', {
		pagination: '.pagination',
		autoplay:3000,
		autoplayDisableOnInteraction:false,
		speed:700,
		grabCursor: true,
		paginationClickable: true,
		loop:true,

	});
	$('.swiper-button-prev.button-prev').on('click', function(e){
		e.preventDefault();
		swiper2.swipePrev()
	})
	$('.swiper-button-next.button-next').on('click', function(e){
		e.preventDefault()
		swiper2.swipeNext()
	})
	//mySwiper.prevButton[0];
	$('.swiper-container2').bind('mouseover',function(){
		swiper2.stopAutoplay();
		if($('.pagination>span').size() == 1){

		}else{
			$('.swiper-container2 .button-prev').css({'display':'block'})
			$('.swiper-container2 .button-next').css({'display':'block'})
		}

	})
	$('.swiper-container2').bind('mouseout',function(){
		loo_map()
		$('.swiper-container2 .button-prev').css({'display':'none'})
		$('.swiper-container2 .button-next').css({'display':'none'})
	})
	loo_map()
	function loo_map(){
		if($('.pagination>span').size() == 1){
			swiper2.stopAutoplay();
			$('.swiper-container2 .button-prev').css({'display':'none'})
			$('.swiper-container2 .button-next').css({'display':'none'})

		}else{
			swiper2.startAutoplay();
		}
	}
});
