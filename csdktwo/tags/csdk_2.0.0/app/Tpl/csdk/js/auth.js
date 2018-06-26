
function auth_point(){
	$('.undone_msg').show();
    $('.gray_box').show();
}
$(function(){
    $('.close_btn').click(function(){
        $(this).parent().hide();
        $('.gray_box').hide();
    });
})
function estp_update(){

	var ajaxurl = APP_ROOT+"/index.php?ctl=auth&act=estp_update";
	var query = new Object();    
	$.ajax({ 
        url: ajaxurl,
        dataType: "json",
        data:query,
        type: "POST",
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {   //alert('ok');
                window.location.href = APP_ROOT+"/auth/estp_show";
                //alert('ok');
            }
            else
            {
                alert('fail');
            }
        },
        error:function(ajaxobj)
        {
            alert("error"); 
        }
    });
}


function investor_update(){

    var ajaxurl = APP_ROOT+"/index.php?ctl=auth&act=investor_update";
    var query = new Object();    
    $.ajax({ 
        url: ajaxurl,
        dataType: "json",
        data:query,
        type: "POST",
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {   //alert('ok');
                window.location.href = APP_ROOT+"/auth/investor_show/side_step-5";
                //alert('ok');
            }
            else
            {
               // alert('fail');
               $('#investor_authenticate').html(ajaxobj.info);
            }
        },
        error:function(ajaxobj)
        {
            //alert("error"); 
            $('#investor_authenticate').html('提交失败');
        }
    });
}
