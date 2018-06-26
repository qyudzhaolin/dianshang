(function($){
	var $parent,json,BokDate,sentAjax,settings;

	$(document).on('click',function(){
		$($parent).find('ul').remove();
		$($parent).find('ul').css({'display':'none'});
	});
	function fntab(options){
		//this 指向
		$parent = this;
		settings = $.extend(settings , options);
		create_label();
		keyup_fn();
		input_table($($parent).attr('id'))

	}
	//键盘输入发送请求
	function keyup_fn(){
		$($parent).bind('keyup',function(){
			if(!$($parent).find('ul').length){
				$($parent).append($('<ul class="ul"></ul>'))
			}
			if($($parent).find('input').val() == ''){
				$($parent).find('i').remove();
				$($parent).find('ul').remove();
				return;
			}
			//abort() 和 数据没有就不发送
			if(sentAjax){
				sentAjax.abort();
			}
			ajax_fn();
		})
	}
	//ajax请求
	function ajax_fn(){
		sentAjax = $.ajax({
			url:settings.url,
			type:'GET',
			cache:false,
			dataType:'json',
			data:{
				m:settings.interface,
				a:settings.method,
				linkValue:$.trim($($parent).find('input').val())
			},
			success:function(data){
				var $Li = '';
				BokDate = data.status;
				json = data.data;
				if(!data.data ){

				}else{
					$.each(data.data,function(i){
						$Li += '<li>'+json[i].name+'</li>';
					})
				}
				$($parent).find('ul').html($Li);
				// 判断有没有数据
				if(BokDate == 0){
					$($parent).find('ul').html('<li>没有数据</li>')
					// $($parent).find('i').html('没有数据')
				}
				//点击某一个数据
				$($parent).find('ul').on('click','li',function(ev){
					var This = this;
					ev.stopImmediatePropagation();
					var indexaaa = $(this).index();
					$($parent).find('input').val(json[indexaaa].name);
					$($parent).find('ul').remove();
					$($parent).find('ul').css({'display':'none'});
					fn_WO(json[indexaaa]);
					settings[(settings.fn)]()
				})
			},
			beforeSend:function(){
				if(typeof(settings.beforeFn) == "function"){
					settings.beforeFn();
				}
				$($parent).find('ul').html('<li>Loading....</li>')
			},
			complete:function(data){
				$($parent).find('ul').css({'display':'block'});
			},
			error:function(){
				console.log('失败')
			}
		})
	}
	//创建节点
	function create_label(){
		$('.input_box_all').append('' +
				'<ul class="ul"></ul>' +
		'') 
	}
	//点击后调用
	function fn_WO(bbb){
		settings.fn1 = function(){
			$('.dealusename').val(bbb.third_data)
			$('#user_id').val(bbb.id)
		};
		settings.fn2 = function(){
			$('input[name="n_deal"]').val(bbb.id)
		};
		settings.fn3 = function(){
			//点击的时候操作
			$('#user_id').val(bbb.id);
			$('#user_mobile').val(bbb.third_data);  
		};
	}

	//没有点击的时候操作
	function input_table(id){
		//关联项目
		// $($parent).find('input[id=dealinptuid]').val($('.dealid').val())
		//推荐人
//		$($parent).find('input[id=dealpersoninputid]').val($('.dealperson').val());  
	}
	$.fn.extend({
		searchAll:fntab
	})
})(jQuery)

