$(function(){
	// 弹层
	$('.showclick').on('click',function(ev){
		ev.stopImmediatePropagation();
		
		// reset data
		$("#fund_name").html($(this).parent().siblings(".set_style_1").find("li:eq(0) p:eq(0) a").html());
		$("#fund_invest_amount").html($(this).parent().siblings(".set_style_3").find(" div div").attr("data-amount"));
		$("#fund_total_amount").html($(this).parent().siblings(".set_style_4").find(" span").text());
		$("#fund_invest_min_amount").html($(this).parent().siblings(".set_style_2").find("div:eq(0) span").text());
		$("#expect_invest_amount").val($(this).attr("data-amount"));
		$("#expect_invest_remark").val($(this).attr("data-remark"));
		$("#fund_raising_id").val($(this).attr("data-id"));
		
		$('.clouedb.popup').find('.bj').css({'display':'block'});
		$('.clouedb.popup').find('ul').css({'display':'block'});
		$('.content.content').css({'display':'block'});
		$('.clouedb').css({'display':'block'})
		$('.clouedb').addClass('popup')
		$('.content').css({'transform':'scale(1)','WeblitTransform':'scale(1)'}).css('transition','.3s')   
		$('.content').get(0).addEventListener('transitionend',function(){
			$('.cloud').css({'display':'block'});
		},false)
		$('.popup').css({'marginLeft':-$('.popup').width()/2}).css({'marginTop':-$('.popup').height()/2})
	})
	
	// 发送意向
	$('.showSend').on('click',function(ev){
		
		ev.stopImmediatePropagation()
		
		// check data
		var fund_invest_min_amount = parseInt($("#fund_invest_min_amount").text());
		var real_invest_min_amount = parseInt($("#expect_invest_amount").val());
		console.log(fund_invest_min_amount);
		console.log(real_invest_min_amount);
		
		if(real_invest_min_amount == 0){
			alert("不能为空！");
			return;
		}else if(isNaN(fund_invest_min_amount) || isNaN(real_invest_min_amount)){
			alert("必须为数字！");
			return;
		}else if(fund_invest_min_amount > real_invest_min_amount){
			alert("拟认购金额必须大于起投金额！");
			return;
		}
		
		var remark = $.trim($("#expect_invest_remark").val());
		var query = new Object();
		query.amount = real_invest_min_amount;
		query.remark = $.trim($("#expect_invest_remark").val());
		query.fid = get_input_value('fund_raising_id');;
		
		var ajaxTimeoutTest = $.ajax({
			url: APP_ROOT+"/index.php?ctl=fund&act=send_investment_intent",
			timeout:20000,
			dataType: "json",
			data:query,
			type: "POST",
			beforeSend: function () {
				$(".showSend").attr('disabled', 'disabled');
			},
			complete: function (XMLHttpRequest,status) {
				if(status == 'timeout'){// 超时,status还有success,error等值的情况
					ajaxTimeoutTest.abort();
					alert('网络超时');
				}
				$(".showSend").removeAttr('disabled')
			},
			success: function(data){
				
				if(data.status == 0){
					$('.content').css({'transform':'scale(0)','WeblitTransform':'scale(0)'}).css('transition','.3s')   
					$('.clouedb').get(0).addEventListener('transitionend',function(){
						$('.cloud-main').addClass('showSendOk').removeClass('cloud-main');
					},false)
				}else{
					alert(data.info);
				}
				
			},
			error:function(ajaxobj){
				alert("服务器错误！");
			}
		});
		     
	})
	
	// 取消按钮
	$('.removeclick').on('click',function(){
		$('.popup').addClass('cloudHide').removeClass('popup')
		$('.clouedb ').css({'display':'none'})
	})
	
	$('.content').on('click',function(){
		$('.content').on('click',function(ev){
			ev.stopPropagation()
		})
	})
	
	// 点击空白，隐藏弹层
	$(document).on('click',function(ev){
		ev.stopImmediatePropagation();   
		$('.clouedb.popup').find('.bj').css({'display':'none'});
		$('.clouedb.popup').find('ul').css({'display':'none'});
		$('.cloud').css({'display':'none'});
		$('.content.content').css({'display':'none'});  
	})

})