$(function(){



    var swiper = new Swiper('.swiper-container', {

        slidesPerView: 3,
        centeredSlides: false,
        paginationClickable: false,
        spaceBetween: 0,

        autoplay : 4000,

        loop : false,




    });
    var outerWidth = ($('.swiper-slide2 div').outerWidth()/2);
    $('.swiper-slide2 div').css({'left':'50%','marginLeft':- outerWidth-4})

    var outerWidth2 = ($('.swiper-slide3 div').outerWidth()/2);
    $('.swiper-slide3 div').css({'left':'50%','marginLeft':- outerWidth2-4});

    var outerWidth3 = ($('.size_width').outerWidth()/2);
    $('.size_width').css({'left':'50%','marginLeft':-outerWidth3});

    $('.i_bar').css({'left':'50%','marginLeft':'-12px'})
    $('.swiper-button-prev').on('click', function(e){
        e.preventDefault();
        swiper.swipePrev()
    });
    $('.swiper-button-next').on('click', function(e){
        e.preventDefault();
        swiper.swipeNext()
    })
    if($('.swiper-wrapper').find('.swiper-slide').size() <=3){
        if($('.swiper-wrapper').find('.swiper-slide').size()==1){
            for(var i=0;i<2;i++){
                $('.swiper-wrapper').append('<div class="swiper-slide"></div>')
            }
        }else if($('.swiper-wrapper').find('.swiper-slide').size()==2){
            $('.swiper-wrapper').append('<div class="swiper-slide"></div>')
        }
    }else if($('.swiper-wrapper').find('.swiper-slide').size() <=6){
        if($('.swiper-wrapper').find('.swiper-slide').size()==4){
            for(var i=0;i<2;i++){
                $('.swiper-wrapper').append('<div class="swiper-slide"></div>')
            }
        }else if($('.swiper-wrapper').find('.swiper-slide').size()==5){
            $('.swiper-wrapper').append('<div class="swiper-slide"></div>')
        }
    }


})





























