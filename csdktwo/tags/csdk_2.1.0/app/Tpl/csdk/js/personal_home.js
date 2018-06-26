$(function(){
	//初始化
    $('.form.bs').css('display','none');
    $('.form.bs.active').css('display','block');
  
	//右侧选项
    $('.Maintain_and_update').find('a').bind('click',function(){
 
        var this_in = $(this).index();
        var oWidth = $('.Maintain_and_update a').eq(this_in).width();
        $('.Maintain_and_update a').removeClass('active');
        $('.Maintain_and_update a').eq(this_in).addClass('active');
        $('.form.bs').removeClass('active').css('display','none');
        $('.form.bs').eq(this_in).addClass('active').css('display','block');
        if( $(this).index() == 0){
            $('.Maintain_and_update div').animate({'left':$('.Maintain_and_update').find('a').eq(this_in).position().left+89,'width':oWidth})
        }else{
            $('.Maintain_and_update div').animate({'left':$('.Maintain_and_update').find('a').eq(this_in).position().left+40,'width':oWidth})
        }
    });
    $('#display_block').css({'display':'block'})
    //立即认证
    $('#is_review_button').bind('click',function(){
    	 
        $('.form.bs').css('display','none');
        $('.active2.infi').css('display','block')
    });
    //提交审核
    $('.examine_cancel.bs').bind('click',function(){
    	$('#get_pwd_msg').html('');
        $('#get_pwd_message').removeClass('warnning');
        $('#get_pwd_message').val('');
        
        $('#get_news_mobile_msg').html('');
        $('#get_news_mobile').removeClass('warnning');
        $('#get_news_mobile').val('');
        
        $('#get_pwd_second_msg').html('');
        $('#get_new_message').removeClass('warnning');
        $('#get_new_message').val('');
        location.href = "#personal_id"; 
        show();
    });
    //解绑旧手机号码
    $('.account_modify.Bound_phone').bind('click',function(){
        $('.form.bs').css('display','none');
        $('.Unbundling').css('display','block');
    });
    //换绑手机号码
    $('.examine_cancel.bs.cancel').bind('click',function(){
    	$('#user_old_pwd_msg').html('');
        $('#user_old_pwd').removeClass('warnning');
        $('#user_old_pwd').val('');
        
        $('#user_new_pwd_msg').html('');
        $('#user_new_pwd').removeClass('warnning');
        $('#user_new_pwd').val('');
        
        $('#user_new_pwd_confirm_msg').html('');
        $('#user_new_pwd_confirm').removeClass('warnning');
        $('#user_new_pwd_confirm').val('');
        $('#get_pwd_third_msg').html('');
        
        show();
    });
    //修改按钮
    $('.account_modify.Change_password').bind('click',function(){
        $('.form.bs').css('display','none');
        $('.form.bs.Change_password').css('display','block')
    })
    //认证审核返回到 账户安全
    $('.save_btn.back.bs').bind('click',function(ev){
    	 
    	window.location.href = APP_ROOT+"/account/";
        ev.preventDefault();
        show();
    });
    //封装当前显现的页面
    function show(){
        $('.form.bs').css('display','none');
        $('.form.bs.pos').css('display','block');
    }
    //��֤�����
    $('.account_cation.center').bind('click',function(){
        $('.form.bs').css('display','none');
        $('.form.bs.Audit.bs').css('display','block')
    });
    
  
    
    $('.account_cation.green').bind('click',function(){
        $('.form.bs').css('display','none');
        $('.form.bs.Audit.bs').css('display','block')
    });

})