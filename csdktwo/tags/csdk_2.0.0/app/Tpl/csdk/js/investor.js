
/****Author: Zhu, HuanJun***********/
/****Date: 2015/7/23******/
/****Functionality: 决定关注BUTTON的点击效果， 并且与DB 交互***/

function add_focus()
{
	var status=true;
	if(status){
		$('.attention').html('已关注TA');
		$('.attention').css({'color':'#666666','cursor':'auto'});
		$('.attention').addClass('selected')
	}

	var execute_url = APP_ROOT+"/index.php?ctl=investor&act=add_focus";
	var project_id =$('#project_id').val();
	

	var user_id =$('#user_id').val();
	
	var json_data ={
		'project_id':project_id
		,'user_id': user_id
	};
	$.post(
		execute_url
		,json_data
		,function(data){
			console.log(data);//call_fun(data);
		}
		,"json"
	);

}
 
/****Author: Zhu***********/
/****Date: 2015/7/14******/
/****Functionality: 进入项目详情时，决定投资相关BUTTON 和 关注BUTTON 的显示***/
$(document).ready(function() {
	var project_follow = $('#project_follow').val();
 
	console.log(project_follow);
	if(project_follow)
	{
		$('.attention').html('已关注TA');
		$('.attention').css({'color':'#666666','cursor':'auto'});
		$('.attention').addClass('selected');
	}
	 
	
}) ;

/*悬浮条*/
$(function() {
    $(window).scroll(function(event) {
        var num=$(window).scrollTop();
        if(num>0){
            $('.ss').show();
        }else{
            $('.ss').hide();
        }
    });
});

$(document).ready(function() {
    if($("#investor_detail").val()!=undefined){
        window.addEventListener("scroll",function(){
               
               change_class("info","info_class"); 
               change_class("investor","investor_class");  
               change_class("view","view_class");             
                             
         });
    }
});

function change_class(pos_id,bar_id){
     var view_top =  $(window).scrollTop(); //可见高度顶部
     var view_bottom =  view_top+$(window).height(); //可见高度底部
     var top=$('#'+pos_id).offset().top;//alert(top);
     
     if(top>=view_top&&view_bottom>=top){
            $(".tt").removeClass('tt');
            $("#"+bar_id).addClass('tt');
      }
}
