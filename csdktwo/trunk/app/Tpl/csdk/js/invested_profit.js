function income_analysis(area){

	var fid = area.attr("data-id");

	var ajaxTimeoutTest = $.ajax({
		url: APP_ROOT+"/index.php?ctl=profit&act=analy",
		timeout:20000,
		dataType: "json",
		data:{fund_id:fid},
		type: "POST",
		beforeSend: function () {
			area.attr('disabled', 'disabled');
			// 发送之前提示
			
		},
		complete: function (XMLHttpRequest,status) {
			if(status == 'timeout'){// 超时,status还有success,error等值的情况
				ajaxTimeoutTest.abort();
				alert('网络超时');
			}
			area.removeAttr('disabled')
		},
		success: function(data){

			if(data.status == 99){

				area.attr("data-type",99);

				var obj = area.parent().next().find(".data-txt");

				var num = 0;
				var totalnum = data.data.sum_investor_profit;          //个人 总基金
				var rightnum = 150;        // 距离 右边 的空隙多大

				var income = data.data.fund_income;
				var assist_rate = data.data.assist_rate;
				
				// 去绝对值数组，来获取最大值
				var income_ = new Array();
				for(var i=0;i<income.length;i++){
					income_[i] = Math.abs(income[i] == '-' ? 0 : income[i]);
				}
				var max_income = Math.max.apply(null, income_);
				
//				for(var i=0;i<income.length;i++){
//					for(var j = i + 1;j<income.length;j++){
//						if(income[i]<income[j]){
//							var tmp = income[i];
//							income[i] = income[j];
//							income[j] = tmp;
//						}
//					}
//				}
				
				var deal_name = data.data.deal_name;
				var deal_name_obj = area.parent().next().find('.name_txt');
				for(var s=0;s<deal_name.length;s++){
					deal_name_obj.append('<div>'+deal_name[s]+'</div>')
				}
				for(var f = 0;f<income.length;f++){
					if(income[f] == '-') {
						income[f] == 0
					}
				}
				for(var q=0;q<assist_rate.length;q++){
					
					var assists = income[q] == '-' ? "" : "("+assist_rate[q]+"%)";
					
					// 用最大值去限制整个区域的宽度
					if(Math.abs(income[q]) == max_income){
						
						if(parseFloat(income[q]) >= 0 || income[q] == '-'){
							obj.find('.side1').append('<div class="d_1"></div>')
							obj.find('.side1 .d_1').eq(q).append('<i><span>'+income[q]+'</span>'+assists+'</i>')
							obj.find('.side1 .d_1').eq(q).width(500 - rightnum)
						}else{
							obj.find('.side2').append('<div class="d_1"></div>')
							obj.find('.side2 .d_1').eq(num).append('<i><span>'+income[q]+'</span>'+assists+'</i>')
							obj.find('.side2 .d_1').eq(num).width(500 - rightnum)
							num++ 
						}
						
					}else{

						if(parseFloat(income[q]) >= 0 || income[q] == '-'){
							// 基金为正的情况
							var tmps = ((income[q]*350)/max_income)
							obj.find('.side1').append('<div class="d_1"></div>')
							obj.find('.side1 .d_1').eq(q).append('<i><span>'+income[q]+'</span>'+assists+'</i>')
							obj.find('.side1 .d_1').eq(q).width(tmps)
						}else{
							// 基金为负数的情况
							var tmps = ((income[q]*350)/max_income)
							obj.find('.side2').append('<div class="d_1"></div>')
							obj.find('.side2 .d_1').eq(num).append('<i><span>'+income[q]+'</span>'+assists+'</i>')
							obj.find('.side2 .d_1').eq(num).width(-tmps)
							num++ 
						}
					}
				}

				// 显示数据
				area.parent().next().slideDown(200,function(){area.addClass('active')});

			}else{
				alert(data.info);
			}

		},
		error:function(ajaxobj){
			alert("获取信息失败")
		}
	});

}

$(function () {

	$('.income_analysis').toggle(
			function(){
				var this_ = $(this);
				var div_none = $(this).parent().next();

				if(div_none.is(':animated')) return;

				if($(this).attr('data-type') == 0){
					income_analysis($(this));
				}else{
					div_none.slideDown(200,function(){this_.addClass('active')});
				}

			},
			function(){
				var this_ = $(this);
				var div_none = $(this).parent().next();
				if(div_none.is(':animated')) return;
				div_none.slideUp(400,function(){this_.removeClass('active')});
			}
	)

});