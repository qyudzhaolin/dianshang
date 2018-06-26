 $(function(){
 	var status=false;
    $('.vtitle a').click(function(){
    	if(!status){
    		$(this).parent().children('a').addClass('cur');
    		$(this).parent().children().eq(1).slideDown();
    		status=true;
    	}else{
    		$(this).parent().children('a').removeClass('cur');
    		$(this).parent().children().eq(1).slideUp();
    		status=false;    		
    	}
        // $(this).children('.vcon').slideDown();
        // $(this).siblings('.vtitle').children('.vcon').slideUp();
    });
 })
