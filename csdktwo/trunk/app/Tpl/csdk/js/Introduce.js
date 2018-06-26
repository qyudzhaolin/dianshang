$(function(){



    var swiper = new Swiper('.swiper-container3', {
        pagination: '.pagination',
        autoplay:3000,
        autoplayDisableOnInteraction:false,
        speed:700,
        grabCursor: true,
        paginationClickable: true,
        loop:true

    });
    $('.swiper-button-prev').on('click', function(e){
        e.preventDefault()
        swiper.swipePrev()
    })
    $('.swiper-button-next').on('click', function(e){
        e.preventDefault()
        swiper.swipeNext()
    })
    $('.swiper-container3').bind('mouseover',function(){
        swiper.stopAutoplay();
        if($('.pagination>span').size() == 1){

        }else{
            $('.swiper-container3 .button-prev3').css({'display':'block'})
            $('.swiper-container3 .button-next3').css({'display':'block'})
        }

    })
    $('.swiper-container3').bind('mouseout',function(){
        loo_map()
        $('.swiper-container3 .button-prev3').css({'display':'none'})
        $('.swiper-container3 .button-next3').css({'display':'none'})
    })
    loo_map()
    function loo_map(){
        if($('.pagination>span').size() == 1){
            swiper.stopAutoplay();
            $('.swiper-container3 .button-prev3').css({'display':'none'})
            $('.swiper-container3 .button-next3').css({'display':'none'})

        }else{
            swiper.startAutoplay();

        }
    }
    //$('.border_1px').bind('mouseenter',function(){
    //    $(this).find('dl').stop().animate({'height':$(this).parent().find('dl').get(0).scrollHeight},100)
    //});
    //$('.border_1px').bind('mouseleave',function(){
    //    $(this).find('dl').stop().animate({'height':'82px'},100)
    //});

    //移入管理团队 模块



    //if($('dd[dataindex]').text($.trim($('dd[dataindex]').text()).length/2) <=32){
    //    $(this).parent().css({'background':'red'})
    //}
    //if($.trim($('dd[dataindex]').text()).length/2 <=32){
    //    $('dd').css({'height':'60px'});
    //    $(this).parent().css({'background':'red'})
    //}

    $('.dl_bar').on('mouseenter','dd',function(ev){
            ev.stopImmediatePropagation();
            $(this).hide();
            $(this).parent().find('img').hide();
            var index = $(this).index();
            var thisHeight = $(this).get(0).scrollHeight;
            var bgDiv = $('<div class="mask_div">'+$(this).html()+'<i></i><span></span></div>');
            if(!$(this).parents('.img_child_border').find('.mask_div').length){
                $(this).parents('li').append(bgDiv)
            }
            var sildWidth = $(this).parent().find('dt').eq(1).outerWidth();
            var sildLeft = $(this).parent().find('dd').eq(0).position().left;
            var img_toz = $('.toz_img').outerWidth();
            if($(this).attr('dataindex') ==0){
                $('.mask_div').css('left',(sildLeft+172));
                $('.mask_div i').css('left','34px')
            }else if($(this).attr('dataindex') ==1){
                $('.mask_div').css('left',(sildLeft+172));
                $('.mask_div i').css('left','34px')
            }else if($(this).attr('dataindex') ==2){
                $('.mask_div').css('left','170px');
                $('.mask_div').css('top','136px');
                $('.mask_div i').css('left','46px')
            }else if($(this).attr('dataindex') ==3){
                $('.mask_div i').css('left','36px')
                $('.mask_div').css('left',(sildLeft+172))
            }else if($(this).attr('dataindex') ==4){
                $('.mask_div').css('left',(sildLeft+172));
                $('.mask_div i').css('left','36px')
            }else if($(this).attr('dataindex') ==5){
                $('.mask_div').css('left',(sildLeft+172));
                $('.mask_div i').css('left','36px')
            }else if($(this).attr('dataindex') ==6){
                $('.mask_div').css('left',(sildLeft+172));
                $('.mask_div i').css('left','18px')
            }else if($(this).attr('dataindex') ==7){
                $('.mask_div').css('left','170px');
                $('.mask_div').css('top','136px');
                $('.mask_div i').css('left','66px')
            }
            $('.mask_div').on('mouseleave',function(ev){
                ev.stopPropagation();
                $(this).parent().find('img').show();
                $(this).parent().find('dd').show();
                $(this).remove();
            });
        });


});