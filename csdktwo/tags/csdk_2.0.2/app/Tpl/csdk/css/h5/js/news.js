$(function(){
	var st = $(this).scrollTop();
	if($('.content').height()<$(window).height()){
		//����û�г������ڵĸ߶�ʱ
		$('.content').css({'padding':'0 0 .15rem 0'});
		if($('.content').height()>$(window).height()-40){
			$('.copyright').css({'top':$(window).height()})
		}else{
			$('.copyright').css({'top':$(window).height()-20})
		}
		
	}else{
		//���ֳ������ڵĸ߶�ʱ
		$('.content').css({'padding':'0 0 .015rem 0'});
		//���ø߶ȷ�����������ʵû��Ҫ
		tab();
	}
	function tab(){
		$(window).scroll(function ()
		{
			$('.copyright').css({'top':($('.content').height() +10)+st})
		});
	}

	
})