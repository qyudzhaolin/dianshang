/*
 * 平台项目

 */
    function choose_tab(obj){
        var aIndustry = document.getElementsByName(obj);
        for(var i=0;i<aIndustry.length;i++){
            aIndustry[i].index = i;
            aIndustry[i].onclick=function(){
            	$(this).parent().parent().find('input[type=hidden]').val($(aIndustry).eq(this.index).val())
                $(this).parent().parent().find('span').removeClass('active')
                $(this).parent().parent().find('span').eq(this.index).addClass('active');
				choose_list(0);

            }
        }
    }
    $("#province,#city").change(function(){
    	$("input[name=biggest_city]").next().removeClass("active");
    	$("#district").val($("#province").val()+"_"+$("#city").val());
    	choose_list(0);
    });
    //项目筛选、更多
    var Bok = false;
    function choose_list(deal_count){
    	if(Bok)return;
    	Bok = true;
		$('.panduan').delegate('span','click',function(ev){
			$('#province option:eq(0)').attr('selected','selected');
			$('#city option:eq(0)').attr('selected','selected');
		})

    	$('#more_deal').html("正在加载");
    	
		if(deal_count == 0){
			$('.invest_box .invest').remove();
			// 回到页面顶部
			if($(document).scrollTop() > 50){
				$("html, body").animate({ scrollTop: 0 }, 120);
			}
		}
		
    	var ajaxurl = APP_ROOT+"/index.php?ctl=deals&act=deal_choose";
        var this_ = $(this);
        var query = new Object(); 
    	query.cate_choose =get_input_value('cate_choose');
    	query.district =get_input_value('district');
    	query.period_id =get_input_value('period_id');
    	 
    	query.sort =get_input_value('sort');
    	query.counter =deal_count; 
    	 
    	var ajaxTimeoutTest=$.ajax({ 
    		url: ajaxurl,
    		timeout:20000,
    		dataType: "json",
    		data:query,
    		type: "POST",
    		success: function(obj){
    			Bok = false;
				var aEle = '';
				if(obj.status == 0&&deal_count==0){
					aEle += '<div class="invest clearfix">';
					aEle += '<p class="p1 font14-none">'+obj.info+'</p>';
					aEle += '</div>';
				}else{
					//alert(obj.count);
					$.each(obj.data,function(k,v){
						aEle += '<div class="invest clearfix active_'+(v.is_effect-2)+'">';
						aEle += '<a href="'+v.url+'" target="_blank"><img src="'+v.image_deal_logo+'" alt="'+v.deal_name+'" width="100" height="100" /></a>'; 
						aEle +='<div><dl>';
						aEle +='<dt><a href="'+v.url+'" target="_blank">'+v.deal_name+'</a></dt>';
						aEle +='<dd>'+v.deal_sign+'</dd>';
						aEle +='</dl><p>';
						aEle +='<span>'+v.deal_province+'</span>';
					    aEle +='<span>'+v.deal_period+'</span>';
						 
						if(v.user_focus_log == 1){
							aEle +='<span class="active" id="foucs_user'+v.id+'">关注&nbsp;'+v.comment_count+'</span>';
						}
						else
						{
							aEle +='<span data-count='+v.comment_count+' onclick="do_focus('+v.id+',\'foucs_user'+v.id+'\')" id="foucs_user'+v.id+'">关注&nbsp;'+v.comment_count+'</span>';
						}
						aEle +='</p></div></div>';
					});
				} 
				
				$('.news_more_box.invest_btn').before(aEle);
				if(obj.status==0){
                    $('#more_deal').hide();
                }else{
                    $('#more_deal').show();
                }
				 
				if(obj.count==deal_count+5){
                    $('#more_deal').hide();
                    
                }
				if(obj.info<5){
                    $('#more_deal').hide(); 
                }
				 
				$('#more_deal').html("显示更多");
			}
    	});	
    }
    
    choose_tab('cate_list');
    choose_tab('biggest_city');
    choose_tab('Round');
    choose_tab('sort');
    
	$("input[type=radio]:eq(0)").trigger('click');
	 
	$('#more_deal').click(function(){
	var deal_count =parseInt($('.invest_box dl').size());
		choose_list(deal_count);
	})


 //   事件委托
var str = '';
var text_str = $('<div class="alt">'+str+'</div>');
$('body').append(text_str);
 $('ul').delegate('span','mouseenter',function(ev){
	 str = $(this).text()
 	 if($(this).html().length>4){
		 $('.alt').css({'display':'block'});
		 $('.alt').css({'top':ev.pageY-$('.alt').outerHeight()-$(window).scrollTop(),'left':ev.pageX+($('.alt').outerWidth()/4)});
		 $('.alt').html(str)
 	 }
 });
$('ul').delegate('span','mouseleave',function(ev){
		$('.alt').css({'display':'none'});
});



    
 