//banner 切换
$(function(){
	var iNow=0;    //累加器    初始为0；
	var znum=10; //变量保存zindex的信息，每点击一次zindex层次加1
	var len=$('.banner ul li').length;  //获取ul中li的个数
	$('#corner_mark li').click(function(e){
		var index=$(this).index();
		$(this).addClass('cur').siblings('li').removeClass('cur');
		$('.banner ul li').eq(index).fadeIn(1000).siblings().fadeOut(1000);
		iNow=index;
	});
	var Time=new Date();
	$('.btn_next').click(function(e){
		znum++;
		iNow++;
		if(iNow>len-1){iNow=0;}
		if(new Date()-Time>1000){
			Time=new Date();
			$('#corner_mark li').eq(iNow).addClass('cur').siblings('li').removeClass('cur');
			$('.banner ul li').eq(iNow).fadeIn(1000).siblings().fadeOut(1000);
		}
		
	});
	$('.btn_pre').click(function(e){
		znum++;
		iNow--;
		if(iNow<0){iNow=len-1;}
 		if(new Date()-Time>1000){
 			Time=new Date();
			$('#corner_mark li').eq(iNow).addClass('cur').siblings('li').removeClass('cur');
			$('.banner ul li').eq(iNow).fadeIn(1000).siblings().fadeOut(1000);
		}
	});

});
// $(function(){
// $('#deal_show_pic').click(function(eeeee) {
// 			window.open('http://www.baidu.com');
// 	    });
// });

$(function(){
	/*radio*/
	((function(){
		$('label').click(function(){
		var radioId = $(this).attr('name');
	    $('label').removeAttr('class') && $(this).attr('class', 'checked');
	    $('input[type="radio"]').removeAttr('checked') && $('#' + radioId).attr('checked', 'checked');
	});
	})());


$(document).ready(function() {
	$("#register_button").click(function(event) {
		$("#register_button").addClass("current");
		$("#login_button").removeClass("current");
	});
	$("#login_button").click(function(event) {
		$("#login_button").addClass("current");
		$("#register_button").removeClass("current");
	});
	$("form").keydown(function(event){
	 	if(event.keyCode==13){
        if (event.srcElement.tagName == "TEXTAREA") { 
        	return true; 
          } 
		 return false;
		}
});

$("input[type=text],input[type=password],textarea").each(function(){
		if($(this).val()==$(this).attr('title'))
			{	
				$(this).addClass("default_font"); 
			} 
		});

}) ; 


((function(){
	var last_text='';
	$('input[type=text],input[type=password],textarea').live('focus',function(){
		last_text=$(this).val();
		$(this).removeClass('default_font');
		if($(this).val()==$(this).attr('title')){
			$(this).val('');
			
		}
	});		

 	$('input[type=text],input[type=password],textarea').live('blur',function(){
		var v=$(this).val();
		var title=$(this).attr("title");
		if(v==''){
			$(this).val(title);
			$(this).addClass('default_font');
		}
		if (v!=title&&v!=''&&v!=last_text){
			$(this).removeClass("warnning");
		}
	});

})());

  	((function(){
        $(".container").click(function(){
             $(".confirm_success").hide();
    })
	})());

});

function get_input_value(id){
	var v=$('#'+id).val();
	var title=$('#'+id).attr('title');
	if(v==title)
	{
		return "";
	}
	return v;
}

function pop_register(){
 	$('.reg_win1,.gray_box').show();
 	$("#register_test,button#register_button").addClass("current");
    $("#logon_test,button#login_button").removeClass("current");
}
function pop_login(){
 	$('.reg_win1').show();
 	$('.gray_box').show();
 	$("#register_test,button#register_button").removeClass("current");
    $("#logon_test,button#login_button").addClass("current");
}
function trim(str){ //删除左右两端的空格  
　　return str.replace(/(^\s*)|(\s*$)/g, "");  
} 

//仅能输入数字
$(function () {
           $("input[own_type='number']").live('keyup',function () {
                  //如果输入非数字，则替换为''
                  this.value = this.value.replace(/[^\d]/g, '');
            })
});


$(function(){  
     $("input,textarea").focus(function(){  
      $(this).addClass("focus_border");  
     }).blur(function(){  
       $(this).removeClass("focus_border");  
     });  
});  

$(function(){  
    $("button#register_button,input#user_name,input#mobile,input#user_pwd,input#message").focus(function(){ 
    $("#register_test,button#register_button").addClass("current");
    $("#logon_test,button#login_button").removeClass("current");
    });
 
    $("button#login_button,input#mobile_login,input#user_pwd_login").focus(function(){ 
    $("#register_test,button#register_button").removeClass("current");
    $("#logon_test,button#login_button").addClass("current");
    });
});


$(function(){
	$('.case_list li .bottom .case_hd').mouseenter (function(e){
		// $(this).closest("li").children('.top').stop().animate({top:"0px"},300);
		$(this).closest("li").children('.top').fadeIn(400);
	});
	$('.case_list li .top').mouseleave(function(event) {
		// $(this).stop().animate({top:'-480px'},300);
		$(this).fadeOut(400);
	});
});
$(function(){
	$('#case_list li .bottom .case_hd').mouseenter (function(e){
		// $(this).closest("li").children('.top').stop().animate({top:"0px"},300);
		$(this).closest("li").children('.top').fadeIn(400);
	});
	$('#case_list li .top').mouseleave(function(event) {
		// $(this).stop().animate({top:'-480px'},300);
		$(this).stop().fadeOut(400);
	});
});

// $(function() {
// 	var num=0;
// 	var speed=500;
// 	$('#corner_mark li').click(function(event) {
// 		$(this).addClass('cur').siblings('li').removeClass('cur');
// 		var iNum=$(this).index();
// 		$('.banner ul').animate({left:-iNum*100+'%'}, speed);
// 		num=iNum;
// 	});
// 	$('.btn_next').click(function(event) {
// 		num++;
// 		if(num>1){num=1;}
// 		$('.banner ul').animate({left:-num*100+'%'}, speed);
// 		$('#corner_mark li').eq(num).addClass('cur').siblings('li').removeClass('cur');
// 	});
// 	$('.btn_pre').click(function(event) {
// 		num--;
// 		if(num<0){num=0;}
// 		$('.banner ul').animate({left:-num*100+'%'}, speed);
// 		$('#corner_mark li').eq(num).addClass('cur').siblings('li').removeClass('cur');
		
// 	});

// });




function pop_notice(){
 	$('.prompt_box').show();
 	$('.gray_box').show();
}

function close_btn_notice(){
 $('.prompt_box').hide();
 $('.gray_box').hide();
}



 