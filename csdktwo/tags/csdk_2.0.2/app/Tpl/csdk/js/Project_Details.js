$(function(){
	//右侧点轨迹
    for(var i=0;i<$('.float').length;i++){
        $('.float').eq(i).attr('id','float0'+(i+1))
    }

    $('.floatCtro').append('<b></b>');
    $('.floatCtro p').append('<i></i>')
    $('.floatCtro b').css({'height':$('.floatCtro p').outerHeight()*$('.floatCtro p').size()-40})

    //   事件委托
    var str = '';
    var text_str = $('<div class="alt">'+str+'</div>');
    $('body').append(text_str);

    text_div('.send_request_box','.touzi_dir')
    text_div('.send_request_box','a.csdq_link')
    function text_div(fu_div,child){
        $(fu_div).delegate(child,'mouseenter',function(ev){
            str = $(this).text();
            if($(this).html().length>8){
                $('.alt').css({'display':'block'});


                $('.alt').css({'top':ev.pageY-$('.alt').outerHeight()-$(window).scrollTop(),'left':ev.pageX+($('.alt').outerWidth()/4)});
                $('.alt').html(str)

            }
        });
        $(fu_div).delegate(child,'mouseleave',function(ev){
            $('.alt').css({'display':'none'});
        });
    }

    var AllHet = $(window).height();
    var mainHet= $('.floatCtro').height();
    var fixedTop = (AllHet - mainHet)/2;
    $('div.floatCtro').css({top:'50%','marginTop':'-175px'});
    $('div.floatCtro p').click(function(){
        var ind = $('div.floatCtro p').index(this)+1;
        var topVal = $('#float0'+ind).offset().top-90;
        $('body,html').animate({scrollTop:topVal},1000)
    })
    $('div.floatCtro a').click(function(){
        $('body,html').animate({scrollTop:0},1000)
    })
    $(window).scroll(scrolls)
    scrolls()
    function scrolls(){
        var f1,f2,f3,f4,f5,f6,f7,f8,bck;
        var fixRight = $('div.floatCtro p');
        var blackTop = $('div.floatCtro a');
        var sTop = $(window).scrollTop();
        fl = $('#float01').offset().top;
        f2 = $('#float02').offset().top;
        if($('#float03').size() == 0){
        }else{
            f3 = $('#float03').offset().top;
        }
        if($('#float04').size() == 0){
        }else{
            f4 = $('#float04').offset().top;
        }
        if($('#float05').size() == 0){
        }else{
            f5 = $('#float05').offset().top;
        }
        if($('#float06').size() == 0){
        }else{
            f6 = $('#float06').offset().top;
        }
        if($('#float07').size() == 0){
        }else{
            f7 = $('#float07').offset().top;
        }
        if($('#float08').size() == 0){
        }else{
            f8 = $('#float08').offset().top;
        }










        var topPx = sTop+fixedTop
        $('div.floatCtro').stop().animate({top:'50%','marginTop':'-175px'});

        if(sTop<=f2-100){
            blackTop.fadeOut(300).css('display','none')
        }
        else{
            blackTop.fadeIn(300).css('display','block')
        }

        if(sTop>=fl){
            fixRight.eq(0).addClass('cur').siblings().removeClass('cur');
        }
        if(sTop>=f2-100){
            fixRight.eq(1).addClass('cur').siblings().removeClass('cur');
        }
        if(sTop>=f3-100){
            fixRight.eq(2).addClass('cur').siblings().removeClass('cur');
        }
        if(sTop>=f4-100){
            fixRight.eq(3).addClass('cur').siblings().removeClass('cur');
        }
        if(sTop>=f5-100){
            fixRight.eq(4).addClass('cur').siblings().removeClass('cur');
        }
        if(sTop>=f6-100){
            fixRight.eq(5).addClass('cur').siblings().removeClass('cur');
        }
        if(sTop>=f7-100){
            fixRight.eq(6).addClass('cur').siblings().removeClass('cur');
        }
        if(sTop>=f8-100){
            fixRight.eq(7).addClass('cur').siblings().removeClass('cur');
        }

    }

    $('.float:last').css('padding','0 99px 130px 99px');
    $('.float:last').find('.setSize_span').css({
        'fontSize': '14px',
        'color': '#6b6b6b',
        'lineHeight': '1.8em'
    });
    $('.float:last').find('ul li').css({'fontSize': '14px',
        'color': '#6b6b6b',
        'lineHeight': '1.8em'})
	//投资意向控制
	$('.send_request,.send_request.bottombutton').bind('click',function(){
		$.ajax({
			url: APP_ROOT+"/index.php?ctl=dealdetails&act=add_invest",
			dataType: "json",
			data:"id="+$(this).attr('data-id'),
			type: "POST",
			beforeSend: function () {
				$('.send_request,.send_request.bottombutton').attr('disabled', 'disabled');
			},
			complete: function (XMLHttpRequest,status) {
				if(status == 'timeout'){// 超时,status还有success,error等值的情况
					ajaxTimeoutTest.abort();
					$("#error_msg").html('提交失败');
				}
				$('.send_request,.send_request.bottombutton').removeAttr('disabled');
			},
			success: function(obj){
				if(obj.status==1){
					$('.send_request,.send_request.bottombutton').css('display','none');
					$('.send_request_msg.end,.send_request_msg.sendbuttonend').css({'display':'block'});
				}
				if(obj.status==99){
					 
					binge(1);
				}
				if(obj.status==0){
					 
					binge(3);
				}
				// 99 未登录 0 失败 1成功
				//alert(obj.info);
			},
		});
	});

})