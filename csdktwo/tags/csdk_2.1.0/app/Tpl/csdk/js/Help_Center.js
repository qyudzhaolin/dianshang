/**
 * Created by Administrator on 2016/3/28.
 */
$(function(){
    //点击展开，收缩
    $(function(){
        $('.vtitle a').click(function(){
        	$(this).toggleClass("cur");
        	if($(this).next().is(":hidden")){
        		$(this).next().slideDown();
//        		$(this).parent().siblings().children('.vcon').slideUp();
        	}else{
        		$(this).next().slideUp();
        	}
        });
    })
})