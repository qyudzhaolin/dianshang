$(function(){
	//右侧点轨迹
	for(var i=0;i<$('.float').length;i++){
		$('.float').eq(i).attr('id','float0'+(i+1))
	}

	$('.floatCtro').append('<b></b>');
	$('.floatCtro p').append('<i></i>')
	$('.floatCtro b').css({'height':$('.floatCtro p').outerHeight()*$('.floatCtro p').size()-40})

	//   事件委托
	var str = '';
	var text_str = $('<div class="alt">'+str+'</div>');
	$('body').append(text_str);

	text_div('.send_request_box','.touzi_dir')
	text_div('.send_request_box','a.csdq_link')
	function text_div(fu_div,child){
		$(fu_div).delegate(child,'mouseenter',function(ev){
			str = $(this).text();
			if($(this).html().length>8){
				$('.alt').css({'display':'block'});


				$('.alt').css({'top':ev.pageY-$('.alt').outerHeight()-$(window).scrollTop(),'left':ev.pageX+($('.alt').outerWidth()/4)});
				$('.alt').html(str)

			}
		});
		$(fu_div).delegate(child,'mouseleave',function(ev){
			$('.alt').css({'display':'none'});
		});
	}
	function toDou(iNum){
		return iNum<10?'0'+iNum:''+iNum;
	}
	var AllHet = $(window).height();
	var mainHet= $('.floatCtro').height();
	var fixedTop = (AllHet - mainHet)/2;
	$('div.floatCtro').css({top:'50%'});
	$('div.floatCtro p').click(function(){
		var ind = $('div.floatCtro p').index(this)+1;
		var topVal = $('#float'+toDou(ind)).offset().top-90;
		$('body,html').animate({scrollTop:topVal},1000)
	})
	$('div.floatCtro a').click(function(){
		$('body,html').animate({scrollTop:0},1000)
	})
	$(window).on('resize',function(){
		$('.floatCtro b').css({'height':$('.floatCtro p').outerHeight()*$('.floatCtro p').size()-40})
	})
	$(window).scroll(scrolls)
	scrolls()

	if($('#float01').offset().top<=72 ){
		$('.floatCtro>p').eq(0).attr('class','cur')

	}
	function scrolls(){

		var f1,f2,f3,f4,f5,f6,f7,f8,f9,f10,f11,f12,f13,f14,bck;
		var fixRight = $('div.floatCtro p');
		var blackTop = $('div.floatCtro a');
		var sTop = $(window).scrollTop();
		fl = $('#float01').offset().top;
		f2 = $('#float02').offset().top;
		if($('#float03').size() == 0){
		}else{
			f3 = $('#float03').offset().top;
		}
		if($('#float04').size() == 0){
		}else{
			f4 = $('#float04').offset().top;
		}
		if($('#float05').size() == 0){
		}else{
			f5 = $('#float05').offset().top;
		}
		if($('#float06').size() == 0){
		}else{
			f6 = $('#float06').offset().top;
		}
		if($('#float07').size() == 0){
		}else{
			f7 = $('#float07').offset().top;
		}
		if($('#float08').size() == 0){
		}else{
			f8 = $('#float08').offset().top;
		}
		if($('#float09').size() == 0){
		}else{
			f9 = $('#float09').offset().top;
		}

		$('#float10').size() == 0? null:f10 = $('#float10').offset().top
		$('#float11').size() == 0? null:f11 = $('#float11').offset().top
		$('#float12').size() == 0? null:f12 = $('#float12').offset().top
		$('#float13').size() == 0? null:f13 = $('#float13').offset().top
		$('#float14').size() == 0? null:f14 = $('#float14').offset().top

		var topPx = sTop+fixedTop
		$('div.floatCtro').stop().animate({top:'50%','marginTop':'-16px'});

		if(sTop<=f2-100){
			blackTop.fadeOut(300).css('display','none')
		}
		else{
			blackTop.fadeIn(300).css('display','block')
		}
		sTop>=fl?fixRight.eq(0).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f2-100?fixRight.eq(1).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f3-100?fixRight.eq(2).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f4-100?fixRight.eq(3).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f5-100?fixRight.eq(4).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f6-100?fixRight.eq(5).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f7-100?fixRight.eq(6).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f8-100?fixRight.eq(7).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f9-100?fixRight.eq(8).addClass('cur').siblings().removeClass('cur'):null;

		sTop>=f10-100 ? fixRight.eq(9).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f11-100 ? fixRight.eq(10).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f12-100 ? fixRight.eq(11).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f13-100 ? fixRight.eq(12).addClass('cur').siblings().removeClass('cur'):null;
		sTop>=f14-100 ? fixRight.eq(13).addClass('cur').siblings().removeClass('cur'):null;

	}

	$('.float:last').css('padding','0 99px 130px 99px');
	$('.float:last').find('.setSize_span').css({
		'fontSize': '14px',
		'color': '#6b6b6b',
		'lineHeight': '1.8em'
	});
	$('.float:last').find('ul li').css({'fontSize': '14px',
		'color': '#6b6b6b',
		'lineHeight': '1.8em'})
		
	// 投资意向控制相关
		
	$('#confirm').on('click',function(){
		$("#cloud-3 .cloud-icon-success").html("您的投资份额已确认，不可再次修改！");
		$('.cloud-main').addClass('showSendOk').removeClass('cloud-main');
		$('#cloud-3').show();
		$('#cloud-3').css({'transform':'scale(1)'});
		$('.cloud .cloud-main').css({'transform':'scale(1)'});
		$('.clouedb').css({'display':'block'});
		$('.clouedb').addClass('popup');
		$('.bj').css({'display':'block'});
	})
	

	// 后面的弹出显现
	function show_clond(){
		$('.clouedb.popup').find('.bj').css({'display':'block'});
		$('.clouedb.popup').find('ul').css({'display':'block'});
		$('.content.content').css({'display':'block'});
		$('.clouedb').css({'display':'block'})
		$('.clouedb').addClass('popup')
		$('.content').css({'transform':'scale(1)','WeblitTransform':'scale(1)'}).css('transition','.3s')   
		$('.content').get(0).addEventListener('transitionend',function(){
			$('.cloud').css({'display':'block'});
			click_We()
		},false)
		$('.popup').css({'marginLeft':-$('.popup').width()/2}).css({'marginTop':-$('.popup').height()/2})
	}

	$('#showclick').on('click',function(ev){
		ev.stopImmediatePropagation();

		$("#expect_invest_amount_msg,#expect_invest_remark_msg").html("");

		$('.clouedb.popup').find('.bj').css({'display':'block'});
		$('.clouedb.popup').find('ul').css({'display':'block'});
		$('.content.content').css({'display':'block'});
		$('.clouedb').css({'display':'block'})
		$('.content').css({'transform':'scale(1)','WeblitTransform':'scale(1)'}).css('transition','.3s')   
		$('.clouedb').addClass('popup')
		$('.popup').css({'marginLeft':-$('.popup').width()/2}).css({'marginTop':-$('.popup').height()/2})
		show_clond()
	})

	$('#expect_invest_amount').on('click',function(ev){
		ev.stopImmediatePropagation();
	})
	$('.cloud-content').on('click',function(ev){
		ev.stopImmediatePropagation();
	})

	click_We()
	//点击完成
	function click_We(){
		$('#send_btn_1').on('click',function(ev){
					ev.stopImmediatePropagation(); 
					$('.popup').addClass('cloudHide').removeClass('popup')
					$('.clouedb ').css({'display':'none'})

					
					if(ev.toElement == $('#cloud-3').get(0)){return;}
					$('.clouedb.popup').find('.bj').css({'display':'none'});
					$('.clouedb.popup').find('ul').css({'display':'none'});
					$('.cloud').css({'display':'none'});
					$('.content.content').css({'display':'none'});  
			})
	}

	// 取消按钮
	$('#removeclick').on('click',function(){
		$('.popup').addClass('cloudHide').removeClass('popup')
		$('.clouedb ').css({'display':'none'})
	})

	$('.content').on('click',function(){
		$('.content').on('click',function(ev){
			ev.stopPropagation()
		})
	})

	click_We()
	//点击完成
	function click_We(){
		$('#send_btn_1').on('click',function(ev){
					ev.stopImmediatePropagation(); 
					$('.popup').addClass('cloudHide').removeClass('popup')
					$('.clouedb ').css({'display':'none'})

					
					if(ev.toElement == $('#cloud-3').get(0)){return;}
					$('.clouedb.popup').find('.bj').css({'display':'none'});
					$('.clouedb.popup').find('ul').css({'display':'none'});
					$('.cloud').css({'display':'none'});
					$('.content.content').css({'display':'none'});  
					$('.content').css({'transform':'scale(0)','WebTransform':'scale(0)','MozTransform':'scale(0)','msTransform':'scale(0)'})
			})
	}

	$('#cloud-3 .showSend').on('click',function(ev){
		ev.stopImmediatePropagation();   
		if(ev.toElement == $('#cloud-3').get(0)){return;}
		$('.clouedb.popup').find('.bj').css({'display':'none'});
		$('.clouedb.popup').find('ul').css({'display':'none'});
		$('.cloud').css({'display':'none'});
		$('.content.content').css({'display':'none'});  
	})

	$('#showSend').on('click',function(ev){ 

		ev.stopImmediatePropagation()

		// check data
		var real_invest_min_amount = get_input_value("expect_invest_amount");
		if(real_invest_min_amount){
			var fund_invest_min_amount = parseInt($("#fund_invest_min_amount").text());
			var real_invest_min_amount = parseInt(real_invest_min_amount.replace(/,/,""));
			if(isNaN(real_invest_min_amount)){
				$("#expect_invest_amount_msg").html("必须为数字");
				return;
			}else if(fund_invest_min_amount > real_invest_min_amount){
				$("#expect_invest_amount_msg").html("拟认购金额必须大于起投金额");
				return;
			}
		}

		var remark 		= $.trim($("#expect_invest_remark").val());
		var query 		= new Object();
		query.amount 	= real_invest_min_amount;
		query.remark 	= $.trim($("#expect_invest_remark").val());
		query.fid 		= get_input_value('fund_raising_id');;

		var ajaxTimeoutTest = $.ajax({
			url: APP_ROOT+"/index.php?ctl=fund&act=send_investment_intent",
			timeout:20000,
			dataType: "json",
			data:query,
			type: "POST",
			beforeSend: function () {
				$("#expect_invest_amount_msg").html("");
				$("#expect_invest_remark_msg").html("正在发送请求...");
				$(".showSend").attr('disabled', 'disabled');
			},
			complete: function (XMLHttpRequest,status) {
				$(".showSend").removeAttr('disabled')
				if(status == 'timeout'){// 超时,status还有success,error等值的情况
					ajaxTimeoutTest.abort();
					$("#expect_invest_remark_msg").html("网络超时");
				}
			},
			success: function(data){

				if(data.status == 0){

					if($.browser.msie){
			               if($.browser.msie && $.browser.version =="9.0"){
			                    // alert("我是 IE9 浏览器");
			                     $('.content').hide() 

							 	$('.cloud-main').addClass('showSendOk').removeClass('cloud-main');
								$('#cloud-3').show()
								$('#cloud-3').css({'transform':'scale(1)'})
								$('.cloud .cloud-main').css({'transform':'scale(1)'})
								$('.clouedb').css({'display':'block'})
								$('.clouedb').addClass('popup')
								$('.bj').css({'display':'block'});
								click_We()
			               } 
			        }

					$('.content').css({'transform':'scale(1)','webkitTransform':'scale(1)','msTransform':'scale(1)','MozTransform':'scale(1)'})
						.css({'transition':'.3s','msTransition':'.3s','WebkitTransition':'.3s','MozTransition':'.3s'})

					click_We()

					function fn(){
						// reset data
						$("#expect_invest_amount").val(data.amount);
						$("#expect_invest_remark").val(data.remark);

						$('.content').css({
									'transform':'scale(0)',
									'WeblitTransform':'scale(0)',
									'MozTransform':'scale(0)',
									'mzTransform':'scale(0)'
									})
									.css({
										'transition':'.3s',
										'WebkitTransition':'.3s',
										'MozTransition':'.3s',
										'msTransition':'.3s'
									});
						$('.clouedb').get(0).addEventListener('transitionend',function(){
							$('.clouedb').addClass('popup')
							$('.cloud').css({'display':'block'});
							$('.cloud-main').addClass('showSendOk').removeClass('cloud-main');
						},false)
					}
					var explorer =navigator.userAgent ;
					//ie 
					if (explorer.indexOf("MSIE") >= 0) {
						
					$('.content').get(0).addEventListener('transitionEnd',fn(),false)
					}
					//firefox 
					else if (explorer.indexOf("Firefox") >= 0) {
					$('.content').get(0).addEventListener('MozTransitionEnd',fn(),false)
					}
					//Chrome
					else if(explorer.indexOf("Chrome") >= 0){
					$('.content').get(0).addEventListener('WebkitTransitionEnd',fn(),false)
					}
					//Opera
					else if(explorer.indexOf("Opera") >= 0){
					$('.content').get(0).addEventListener('oTransitionEnd',fn(),false)
					}
					//Safari
					else if(explorer.indexOf("Safari") >= 0){
					$('.content').get(0).addEventListener('transitionend',fn(),false)
					} 
					//Netscape
					else if(explorer.indexOf("Netscape")>= 0) { 
					$('.content').get(0).addEventListener('transitionend',fn(),false)
					} else{
						$('.content').get(0).addEventListener('transitionend',fn(),false)
					}
						







					
                   
				}else{
					if(data.status == 2){
						$("#expect_invest_amount_msg").html(data.info);
					}else{
						$("#expect_invest_remark_msg").html(data.info);
					}
				}
			},
			error:function(ajaxobj){
				$("#expect_invest_remark_msg").html("服务器错误");
			}
		});

	})
})