$(function(){
    var scrollWidth = $('.user').find('a').eq(0).outerWidth();
    $('.investor_page').css({'zIndex':'100','top':($(document).scrollTop()+70),'left':$('.bj_corner').offset().left});

    $(window).resize(function(){
        $('.investor_page').css({'zIndex':'100','top':($(document).scrollTop()+70),'left':$('.bj_corner').offset().left});
    });

    $('.horn_img').css('bottom','0px');
    $(window).scroll(function(){
        if($(document).scrollTop()==0){
            $('.change_box').css({'zIndex':'11'});
            $('.header_box').css('zIndex','12');
            $('.horn_img').css('bottom','0px');
            $('.investor_page').css({'zIndex':'100','top':($(document).scrollTop()+70),'left':$('.bj_corner').offset().left})
        }else if($(document).scrollTop()>0){
            $('.investor_page').css({'zIndex':'100','top':($(document).scrollTop()+70),'left':$('.bj_corner').offset().left})
            $('.change_box').css('zIndex',0);
            $('.horn_img').css('bottom','0px');
            $('.header_box').css({'top':'0','zIndex':'12'})
        }
    });
    
    // var tell = null;
    // $('.bj_corner').mouseover(function(){
    //     clearInterval(tell);
    //     $('.investor_page').css('display','block')
    // });
    // $('.bj_corner').mouseout(function(){
    //     tell = setInterval(function(){
    //         $('.investor_page').stop().css('display','none')
    //     },500)
    // });
    // $('.investor_page').mouseover(function(){
    //     clearInterval(tell);
    //     $('.investor_page').css('display','block')
    // });
    // $('.investor_page').mouseout(function(){
    //     tell = setInterval(function(){
    //         $('.investor_page').stop().css('display','none')
    //     },500)
    // });


    $('.header_infi a').click(function(){
        $('.header_infi a').removeClass('infi_active');
        $(this).addClass('infi_active');
    });

    $("input[own_type='number']").on('keyup',function () {
		//如果输入非数字，则替换为''
		this.value = this.value.replace(/[^\d]/g, '');
	})
	
	$("input[type=text],input[type=password],textarea,select").on('focus',function(){
    	$(this).attr({placeholder: ''});
    	$(this).addClass('default_font_flue');
    }) ;  
    $("input[type=text],input[type=password],textarea,select").on('blur',function(){
    	var title=$(this).attr('title');
    	$(this).attr({placeholder: title});
    	$(this).removeClass('default_font_flue');
    }) ;  

})

function get_input_value(id){
	var v=$('#'+id).val();
	var title=$('#'+id).attr('title');
	// 此处逻辑若修改，请兼顾旧版本，因别处有使用
	if(v==title)
	{
		return "";
	}
	return v;
}

function trim(str){ //删除左右两端的空格  
	return str.replace(/(^\s*)|(\s*$)/g, "");  
} 