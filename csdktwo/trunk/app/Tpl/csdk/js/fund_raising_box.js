$(function(){

		//完成
	$('.confirm').on('click',function(){
		$("#cloud-3 .cloud-icon-success").html("您的投资份额已确认，不可再次修改！");
		$('.cloud-main').addClass('showSendOk').removeClass('cloud-main');
		$('#cloud-3').show()
		$('#cloud-3').css({'transform':'scale(1)'})
		$('.cloud .cloud-main').css({'transform':'scale(1)'})
		$('.clouedb').css({'display':'block'})
		$('.clouedb').addClass('popup')
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

	// 弹层
	$('.showclick').on('click',function(ev){
		
		
		// reset data
		$("#fund_name").html($(this).parent().siblings(".set_style_1").find("li:eq(0) p:eq(0) a").html());
		$("#fund_invest_amount").html($(this).parent().siblings(".set_style_3").find(" div div").attr("data-invest-amount"));
		$("#fund_total_amount").html($(this).parent().siblings(".set_style_4").find(" span").text());
		$("#fund_invest_min_amount").html($(this).parent().siblings(".set_style_2").find("div:eq(0) span").text());
		$("#expect_invest_amount").val($(this).attr("data-amount"));
		$("#expect_invest_remark").val($(this).attr("data-remark"));
		$("#fund_raising_id").val($(this).attr("data-fid"));
		$("#cloud-3 .cloud-icon-success").html("意向投资已经发送！");

		
		show_clond()
	})

	$('#expect_invest_amount').on('click',function(ev){
		
	})
	$('.cloud-content').on('click',function(ev){
		
	})

	
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

	// 发送意向
	$('#showSend').on('click',function(ev){
		
		

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

					$('.content').css({'transform':'scale(0)','webkitTransform':'scale(0)','msTransform':'scale(0)','MozTransform':'scale(0)'})
						.css({'transition':'.3s','msTransition':'.3s','WebkitTransition':'.3s','MozTransition':'.3s'})    


					function fn(){
						
						// reset data
						$('#showclick_'+query.fid).attr('data-amount',data.amount);
						$('#showclick_'+query.fid).attr('data-remark',data.remark);

						$('.cloud-main').addClass('showSendOk').removeClass('cloud-main');
						$('.cloud .showSendOk').css({
										'transform':'scale(1)',
										'WeblitTransform':'scale(1)',
										'MozTransform':'scale(1)',
										'msTransform':'scale(1)'})
									.css({
										'transition':'.3s',
										'WebkitTransition':'.3s',
										'MozTransition':'.3s',
										'msTransition':'.3s'})

						

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
					
					
					
					
					$("#expect_invest_remark_msg,#expect_invest_amount_msg").html("");
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

	// 取消按钮
	$('#removeclick').on('click',function(ev){
		
		$('.popup').addClass('cloudHide').removeClass('popup')
		$('.clouedb ').css({'display':'none'})

		
		if(ev.toElement == $('#cloud-3').get(0)){return;}
		$('.clouedb.popup').find('.bj').css({'display':'none'});
		$('.clouedb.popup').find('ul').css({'display':'none'});
		$('.cloud').css({'display':'none'});
		$('.content.content').css({'display':'none'});  
	})

})





	

