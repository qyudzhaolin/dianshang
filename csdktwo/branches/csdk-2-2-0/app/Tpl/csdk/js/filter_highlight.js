$(function () { 
            //分类
             $("#cates_all").click(function () { 

             	$("[name='cates_names']").each(function(){
						$(this).attr("checked","");
					    $(this).removeClass("blue"); 

				});
				 $("#cates_all").attr("checked","checked");
             }) 
  
            $("#cates").find("a").click(function () { 
				
				if($(this).attr('id')=="cates"){
				return;
				}

            	var check_count=0;
				$("[name='cates_names']").each(function(){

				if($(this).attr("class")=='blue'){
						check_count++;
					}
				});
				 
			 if($(this).attr("class")=='blue') {
						$(this).attr("checked","");
					    $(this).removeClass("blue");

					     if(check_count==1){
                 		$("#cates_all").attr("checked","checked");
					    $("#cates_all").addClass("blue");
                   	 }
                   return;
			 }
				
				if(check_count>=3){
					alert("最多只能选择3项");
					return;
				}

                 $(this).addClass("blue"); 
                 $(this).attr("checked","checked"); 

                 $("#cates_all").attr("checked","");
				 $("#cates_all").removeClass("blue");
            }) 

 		//融资阶段 
       	$("#periods_all").click(function () { 
             	$("[name='periods_names']").each(function(){
					$(this).attr("checked","");
					   $(this).removeClass("blue"); 
				});
				 $("#periods_all").attr("checked","checked");
				 $("#periods_all").addClass("blue");	
             }) 
  
       $("#periods").find("a").click(function () { 
			if($(this).attr('id')=="periods_all"){
				return;
			}
				var check_count=0;
				$("[name='periods_names']").each(function(){	
				if($(this).attr("class")=='blue'){
								check_count++;
							}
				});

			 if($(this).attr("class")=='blue') {
						$(this).attr("checked","");
					    $(this).removeClass("blue"); 

					    if(check_count==1){
                 		 	$("#periods_all").attr("checked","checked");
					    	$("#periods_all").addClass("blue");	
                		 }
                       return;
			  }

				if(check_count>=3){
					alert("最多只能选择3项");
					return;
				}

                $(this).addClass("blue"); 
                $(this).attr("checked","checked"); 
                $("#periods_all").attr("checked","");
				$("#periods_all").removeClass("blue");
            }) 

       //单选融资阶段 
       $("#radio_periods").find("a").click(function () { 
			 if($(this).attr("class")=='blue') {
					$(this).removeClass("blue"); 
			  }
			  else{
                	   $("[name='periods_names']").each(function(){	
						if($(this).attr("class")=='blue'){
								$(this).removeClass("blue"); 
							}
						});     
                	    $(this).addClass("blue"); 
				}
            }) 



       }); 
