$(function(){
		$('.attention_item li:odd').addClass('no_margin');
		$('.attention_item_box:odd').addClass('no_margin');
		$('#my_attention_item:odd').addClass('no_margin');
		$('.memb_list:odd').addClass('no_margin');
		var status=true;
		$('.filtrate_box').click(function(){
			if(status){
				$('.deals_box').show();
				$('.filtrate').hide();
				status=false;
			}
		});
		$('.react_top').click(function(){
			$('.deals_box').hide();
			$('.filtrate').show();
			status=true;
		});
	
});

$(function(){
	var i=0;
	$("#three_row li").each(function(){
	    i=i+1;
	    if(i%3==0){
	    	$(this).addClass('no_margin');
	    }
	});
});
   
     //提交
        function deals_post(){

    	 	if($("#cates_all").attr("checked")) {	
			}
        	if($("#periods_all").attr("checked")) {
			}
             var periods="";
             var cates="";
			 $("[name='periods_names']").each(function(){
				if($(this).attr("class")=='blue'){
							var v=$(this).attr("value");
							 periods+=v+"_";
							}
				});

			$("[name='cates_names']").each(function(){
				if($(this).attr("class")=='blue'){
							var v=$(this).attr("value");
							 cates+=v+"_";
					}
				});
				
		

			 if(periods!=''){
				periods=periods.substring(0,periods.length-1);
				 periods='&periods='+periods;
			 }
			  if(cates!=''){
				  cates=cates.substring(0,cates.length-1);
				 cates='&cates='+cates;
			 }
			 //地区
			var district="";
			var province=$("[name='province']").val();
			var city=$("[name='city']").val();
			if(province!=''){
			    district="&district="+province+"_"+city;
			}
            var url= APP_ROOT+"/index.php?ctl=deals&act=index"+periods+cates+district;
            location.href = url;
            //alert(url);
            
        } 



        function load_city()
		{
				var id = $("select[name='province']").find("option:selected").attr("rel");
				
				var evalStr="regionConf.r"+id+".c";

				if(id==0)
				{
					var html = "<option value=''>全部</option>";
				}
				else
				{
					var regionConfs=eval(evalStr);
					evalStr+=".";
					if (id) {
						var html = "";
					}else{
						var html = "<option value=''>全部</option>";
					}
					
					for(var key in regionConfs)
					{
						html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
					}
				}
				$("select[name='city']").html(html);		
		}



/****Author: Zhu***********/
/****Date: 2015/7/14******/
/****Functionality: LOADING District Data afer DOM has filled in browser***/
$(document).ready(function() {
	if ($("#next_page").attr('tag')) {
	 	$('.deals_box').show();
	 	$('.filtrate').hide();
	 }
	//load_city();
}) ; ; 

function next_page(page_num){
	if($("#cates_all").attr("checked")) {
			}
        	if($("#periods_all").attr("checked")) {
			}
             var periods="";
             var cates="";
			 $("[name='periods_names']").each(function(){
				if($(this).attr("class")=='blue'){
							var v=$(this).attr("value");
							 periods+=v+"_";
							}
				});

			$("[name='cates_names']").each(function(){
				if($(this).attr("class")=='blue'){
							var v=$(this).attr("value");
							 cates+=v+"_";
					}
				});
				
		

			 if(periods!=''){
				periods=periods.substring(0,periods.length-1);
				 periods='&periods='+periods;
			 }
			  if(cates!=''){
				  cates=cates.substring(0,cates.length-1);
				 cates='&cates='+cates;
			 }
			 //地区
			var district="";
			var province=$("[name='province']").val();
			var city=$("[name='city']").val();
			if(province!=''&&city!=''){
			    district="&district="+province+"_"+city;
			}
            var url= APP_ROOT+"/index.php?ctl=deals&act=index&page="+page_num+periods+cates+district;
            location.href = url;
            //alert(url);
}

