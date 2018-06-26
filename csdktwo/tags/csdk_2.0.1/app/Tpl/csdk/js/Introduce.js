$(function(){



    var swiper = new Swiper('.swiper-container3', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        autoplay:2000,
        autoplayDisableOnInteraction:false,
        speed:500,
        prevButton:'.swiper-button-prev',
        nextButton:'.swiper-button-next',
        loop:true,

    });
    $('.swiper-container3').bind('mouseover',function(){
        swiper.stopAutoplay();
        if($('#swiper-pagination3>span').size() == 1){

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
        if($('#swiper-pagination3>span').size() == 1){
            swiper.stopAutoplay();
            $('.swiper-container3 .button-prev3').css({'display':'none'})
            $('.swiper-container3 .button-next3').css({'display':'none'})

        }else{
            swiper.startAutoplay();

        }
    }
    $('.border_1px').bind('mouseenter',function(){
        $(this).find('dl').stop().animate({'height':$(this).parent().find('dl').get(0).scrollHeight},100)
    })
    $('.border_1px').bind('mouseleave',function(){
        $(this).find('dl').stop().animate({'height':'67px'},100)
    })

})