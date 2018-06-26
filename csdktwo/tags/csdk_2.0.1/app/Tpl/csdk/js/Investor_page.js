$(function(){
    var swiper = new Swiper('.swiper-container4', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        autoplay:2000,
        autoplayDisableOnInteraction:false,
        speed:500,
        prevButton:'.swiper-button-prev',
        nextButton:'.swiper-button-next',
        loop:true,

    });
    $('.swiper-container4').bind('mouseover',function(){
        swiper.stopAutoplay();
        if($('#swiper-pagination4>span').size() == 1){

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
        if($('#swiper-pagination4>span').size() == 1){
            swiper.stopAutoplay();
            $('.swiper-container4 .button-prev4').css({'display':'none'})
            $('.swiper-container4 .button-next4').css({'display':'none'})

        }else{
            swiper.startAutoplay();

        }
    }

})