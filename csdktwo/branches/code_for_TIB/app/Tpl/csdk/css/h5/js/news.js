$(function(){
	var st = $(this).scrollTop();
	if($('.content').height()<$(window).height()){
		//文字没有超出窗口的高度时
		$('.content').css({'padding':'0 0 .15rem 0'});
		if($('.content').height()>$(window).height()-40){
			$('.copyright').css({'top':$(window).height()})
		}else{
			$('.copyright').css({'top':$(window).height()-20})
		}
		
	}else{
		//文字超出窗口的高度时
		$('.content').css({'padding':'0 0 .015rem 0'});
		//调用高度方法，这里其实没必要
		tab();
	}
	function tab(){
		$(window).scroll(function ()
		{
			$('.copyright').css({'top':($('.content').height() +10)+st})
		});
	}

	
})