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
		'<div class="Prompt" style="border-bottom:none;line-height:74px">登录提示</div>' +
		'<div style="position: absolute;top:64px;overflow: hidden;height:162px;">' +
		'<div class="b" id="b"><div class="set_p_style_1"><label for="mobile"><i></i></label><input type="text" placeholder="输入您的11位手机号码" name="mobile" id="mobile"  title="输入您的11位手机号码" style="width:198px;padding-left: 37px;"/><p class="error_msg" id="mobile_error_msg"></p></div>'+
		'<p  class="set_p_style_2"><label for="pwd"><i></i></label><input type="password"  placeholder="输入登录密码" name="pwd" id="pwd" title="输入登录密码" style="width:198px;padding-left: 37px;margin-top:22px;"/><span class="error_msg" id="pwd_error_msg"></span></p></div>' +
		'</div>' +
		'<div style="height:102px;position: absolute;top:160px;left:0;overflow: hidden;width:400px;">' +
		'<div class="box" style="top:'+json.setBox_buttom+'px;display:'+json.box_display+'">' +
		'<a href="#" class="Sign_In active" id="signin">'+json.Sign_In+'</a>' +
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
		'<a href="/account/" class="register center active ">'+json.register+'</a>' +
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
	t1.to([$('.mask')],0,{width:$(document,'body','html').width(),height:$(window).height()})
	.from([$('.mask')],0,{opacity:0,zIndex:0})
	.from([$('.text_div')],.4,{scale:0,ease:Back.easeOut})
	.from([$('.b')],0,{top:200})
	.from([$('.box')],0,{top:300});
	TweenLite.to([$('.Sign_In'),$('.register')],0,{});
	$('#i_exit').bind('click',function(){
 
		$(".text_div").remove();
		$(".mask").remove();
	})


	// 暂时写的 click
	$('.Sign_In.active').on('click',function(){
		signin();
	})
	
	//添加动画、删除动画步骤
	function anim_set(){
		$('.text_div').addClass('anim_1')   
		$(".text_div").one("webkitAnimationEnd MozAnimationEnd animationEnd",function(){
			$('.text_div').removeClass('anim_1')
		})
	}
	function signin(){
		if(!checkMobile('mobile','mobile_error_msg')){
			anim_set()
			return;
		}
		if(!checkLoginPwd('pwd','pwd_error_msg')){
			anim_set()
			return;
		}
		// if(!checkMobile('mobile','mobile_error_msg')) return;
		 
		
		var ajaxurl = APP_ROOT+"/index.php?ctl=user&act=dosignin";
		var query = new Object();
		query.mobile = get_input_value('mobile');
		query.pwd = get_input_value('pwd');

		var ajaxTimeoutTest = $.ajax({
			url: ajaxurl,
			timeout:20000,
			dataType: "json",
			data:query,
			type: "POST",
			beforeSend: function () {
				$("#signin").attr('disabled', 'disabled');
				$("#pwd_error_msg").html('正在登录...');
			},
			complete: function (XMLHttpRequest,status) {
				if(status == 'timeout'){// 超时,status还有success,error等值的情况
					ajaxTimeoutTest.abort();
					$("#pwd_error_msg").html('网络超时');
				}
				$("#signin").removeAttr('disabled')
			},
			success: function(data){
				$('#pwd_error_msg').html('');
				
				anim_set()
				$('#'+data.data+'_error_msg').html(data.info);
				if(data.data){
					console.log(data.data)
					anim_set()
				}else{
					$('#pwd_error_msg').html(data.info);
				}
				$("#signin").removeAttr('disabled')
				if(data.status == 1){
					window.location.reload();
				}
			},
			error:function(ajaxobj){
				anim_set()
				$('#pwd_error_msg').html('登录失败');
				$("#signin").removeAttr('disabled')
			}
		});
	}

}
