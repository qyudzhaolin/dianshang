
 function choice_notice_close(){
 	$('.choice_success').hide();
 }

 function apply_help_update(){

	var ajaxurl = APP_ROOT+"/choice/apply_help_update";
	var query = new Object();    
	$.ajax({ 
        url: ajaxurl,
        dataType: "json",
        data:query,
        type: "POST",
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {   //alert('ok');
                $('.choice_success').show();
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