$(function(){


    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        slidesPerView: 3,
        centeredSlides: false,
        paginationClickable: false,
        spaceBetween: 0,
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        autoplay : 4000,
//        autoplayDisableOnInteraction : false,
        loop : false,
        onInit: function(swiper){
            var outerWidth = ($('.swiper-slide2 div').outerWidth()/2);
            $('.swiper-slide2 div').css({'left':'50%','marginLeft':- outerWidth-4})

            var outerWidth2 = ($('.swiper-slide3 div').outerWidth()/2);
            $('.swiper-slide3 div').css({'left':'50%','marginLeft':- outerWidth2-4});

            var outerWidth3 = ($('.size_width').outerWidth()/2);
            $('.size_width').css({'left':'50%','marginLeft':-outerWidth3});

            $('.i_bar').css({'left':'50%','marginLeft':'-10.5px'})
        }


    });



})





























