$(function(){
    var swiper = new Swiper('.swiper-container4', {
        pagination: '.pagination',
        autoplay:3000,
        autoplayDisableOnInteraction:false,
        speed:700,
        grabCursor: true,
        paginationClickable: true,
        loop:true,

    });
    $('.swiper-button-prev').on('click', function(e){
        e.preventDefault()
        swiper.swipePrev()
    })
    $('.swiper-button-next').on('click', function(e){
        e.preventDefault()
        swiper.swipeNext()
    })
    $('.swiper-container4').bind('mouseover',function(){
        swiper.stopAutoplay();
        if($('.pagination>span').size() == 1){

        }else{
            $('.swiper-container4 .button-prev4').css({'display':'block'})
            $('.swiper-container4 .button-next4').css({'display':'block'})
        }

    })
    $('.swiper-container4').bind('mouseout',function(){
        loo_map()
        $('.swiper-container4 .button-prev4').css({'display':'none'})
        $('.swiper-container4 .button-next4').css({'display':'none'})
    })
    loo_map()
    function loo_map(){
        if($('.pagination>span').size() == 1){
            swiper.stopAutoplay();
            $('.swiper-container4 .button-prev4').css({'display':'none'})
            $('.swiper-container4 .button-next4').css({'display':'none'})

        }else{
            swiper.startAutoplay();

        }
    }

})