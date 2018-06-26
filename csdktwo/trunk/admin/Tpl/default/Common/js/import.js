$(function(){
	$('.user_data').bind('click',function(){
		importdata();
	});

    function  importdata(id,mobile){
   id=1;
   var mobile=$('input[name="mobile"]').val();
    var login_type=$('#login_type').val();
    var log_begin_time=$('input[name="log_begin_time"]').val();
    var log_end_time=$('input[name="log_end_time"]').val();
    location.href = ROOT + '?m=Log&a=user_index&id='+id+'&mobile='+mobile+'&log_begin_time='+log_begin_time+'&log_end_time='+log_end_time+'&login_type='+login_type;
    }
})