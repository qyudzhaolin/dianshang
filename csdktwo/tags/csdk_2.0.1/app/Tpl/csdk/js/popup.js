//背景弹出层



function binge(login){

	var setBox_buttom = null;
 
	if(login=="1"){
	var json = {
		'name':'您还未登录，请先登录或免费注册',
		'Sign_In':'登录',
		'register':'注册',//
		setBox_buttom:'30', // box 距离上面的距离
		'box_display':'block'//box 的块是否显现
	}
	}
	else if(login=="3"){
		var json = {
				'name':'尊敬的投资人，认证后才可发送投资意向',
				'Sign_In':'取消',
				'register':'立即认证',//
				setBox_buttom:'30', // box 距离上面的距离
				'box_display':'block'//box 的块是否显现
			}
	}
	else{
		var json = {
				'name':'尊敬的投资人，您必须完成认证后才可推荐项目',
				'Sign_In':'取消',
				'register':'立即认证',//
				setBox_buttom:'30', // box 距离上面的距离
				'box_display':'block'//box 的块是否显现
			}
	}
	if(login=="1"){
	$('body').append('<div class="mask"></div>' +
		'<div class="text_div">' +
		'<a href="#" id="i_exit" class="Sign_In2"></a>' +
		'<div class="Prompt">提示</div>' +
		'<div style="position: absolute;top:114px;overflow: hidden;height:162px;">' +
		'<div class="b" id="b">'+json.name+'</div>' +
		'</div>' +
		'<div style="height:102px;position: absolute;top:160px;left:0;overflow: hidden;width:400px;">' +
		'<div class="box" style="top:'+json.setBox_buttom+'px;display:'+json.box_display+'">' +
		'<a href="/user/signin" class="Sign_In active">'+json.Sign_In+'</a>' +
		'<a href="/user/signup" class="register">'+json.register+'</a>' +
		'</div>' +
		'</div>' +
		'</div>')
	}else
	{$('body').append('<div class="mask"></div>' +
		'<div class="text_div">' +
		'<a href="#" id="i_exit" class="Sign_In2"></a>' +
		'<div class="Prompt">提示</div>' +
		'<div style="position: absolute;top:114px;overflow: hidden;height:162px;">' +
		'<div class="b" id="b">'+json.name+'</div>' +
		'</div>' +
		'<div style="height:102px;position: absolute;top:160px;left:0;overflow: hidden;width:400px;">' +
		'<div class="box" style="top:'+json.setBox_buttom+'px;display:'+json.box_display+'">' +
		'<a href="/home/" class="register center active ">'+json.register+'</a>' +
		'</div>' +
		'</div>' +
		'</div>')	}
	setBox_buttom = $('.box').position().top+$('.box').height();
	mask()
	var timer_this=null;
	var num = 0;
	timer_this=setInterval(function(){
		mask()
	},1)
//
	function mask(){
		$('.mask').css({'position':'absolute','top':'0px'});
		$('.mask').css({'width':$('body,html').width(),'height':$(document,'body','html').height()})
	}
	$(window).resize(function(){
		mask()
	})
	var t1 = new TimelineMax();
	t1.to([$('.mask')],.2,{width:$(document,'body','html').width(),height:$(window).height(),ease:Back.easeOut},'-=.4').from([$('.mask')],.7,{opacity:0,zIndex:0,ease:Back.easeOut},'-=.2').from([$('.text_div')],.7,{scale:0,ease:Back.easeOut},'-=.2').from([$('.b')],1,{top:200,ease:Back.easeOut},'-=.2').from([$('.box')],1,{top:300,ease:Back.easeOut},'-=0.8').from([$('.box')],1,{});
	TweenLite.to([$('.Sign_In'),$('.register')],10,{});
	$('#i_exit').bind('click',function(){
 
		$(".text_div").remove();
		$(".mask").remove();
	})






}
