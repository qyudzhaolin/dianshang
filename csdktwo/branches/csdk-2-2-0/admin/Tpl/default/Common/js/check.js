function csdk_check(json)
{ // alert(1);
	  $("span[name='error_msg']").each(function () {
             $(this).html('');
        }); 
      var is_ok=true;
	  $.each(eval("("+json+")"),function (i, obj_json){
          
		if(obj_json.box==1)
	  	{  //alert(1);
 				var index=0;
 				var is_all_empty=true;
	  		  	$("[name='"+obj_json.name+"']").each(function(){ 
					 var objs=obj_json.input;
					 var is_empty=true;

		  			 for(var i=0; i<objs.length; i++){  
		  			 	var obj=$(this).find("[name='"+objs[i].name+"']");
	    		 	    var v= obj.val();
	    		 	    var title=obj.attr('title');
	    		 	   	if(v==title){
				      		v='';
				      	}
						 if(v.length>0) {//一行不为空
							is_empty=false;
					 	 }

					 	 if(v.length>0){//不全部为空
					 	 	is_all_empty=false;
					 	 }
	 				}  

	 				 for(var i=0; i<objs.length; i++){  
		  			 	var obj=$(this).find("[name='"+objs[i].name+"']");
	    		 	    var v= obj.val();
	    		 	    var title=obj.attr('title');
	    		 	   	if(v==title){
				      		v='';
				      	}
					
	    		 	   	 if (is_empty==false&&v.length==0) { 	//alert(110+objs[i].name+v);

	    		 	   	 	 is_ok=set_warnning(obj,obj_json.error_id,"必填");
	    		 	   	 	// return;
	    		 	   	 }
	    		 	   	 else if(is_empty==false&& v.length>0){
							   var ret=check_input(objs[i],obj,obj_json.error_id);//alert(objs[i].name);
				 		 	   if(is_ok) {
				 		 	   		is_ok=ret;
				 		 	   }
	    		 	   	 }	
	 				}  

	 				if(obj_json.required==true && is_empty==true){
	 				  var o=$("[name='"+obj_json.name+"']");//alert(112);
					   is_ok=set_warnning(o,obj_json.error_id,"必填");
	 				}

	 				//if(!is_ok) {return;}

	 			    index++;
				});
	  		//  			alert(obj_json.required);
				if(obj_json.required==true && is_all_empty){ 
					  var o=$("[name='"+obj_json.name+"']");//alert(113);
					  is_ok=set_warnning(o,obj_json.error_id,"必填");
					  //if (!is_ok) {return;}
				}
 		 }
 		 else{
 		 
 		 		var obj=$("[name='"+obj_json.name+"']"); 
 		 		var ret=check_input(obj_json,obj,obj_json.error_id);
 		 	  if(is_ok) {
 		 	   	is_ok=ret;
 		 	   }
 		 	//if (!is_ok) {return;}
 		 }
	     
	 });

	  if(!is_ok){

   	$(".warnning").each(function () {
            $(this).focus();
             //alert(1);
              return false;
        }); 
	  }
	
	//alert(is_ok);
	 return is_ok;
}

function check_input(obj_json,obj,error_id)
 { 			
  			var is_ok=true;
	      	var value=obj.val(); 
	      	/*if(obj.prop("tagName")=="TEXTAREA"){
	      		value=obj.html();alert(value);
	      	}*/
	      	//alert(obj_json.name+value);
	      	var title=obj.attr('title');
	      	if(value==title){
	      		value='';
	      	}
		// alert(obj.prop("tagName")+value);
		    	if(obj_json.required==true) {
		      	 //验证必填项
			      	if(value==''){//alert(obj_json.name);

			      			is_ok=set_warnning(obj,error_id,"必填");
			      		 	return is_ok;
			      	}
				}
				else{
					if(value==''){//alert(obj_json.name);
			      		return is_ok;
			      	}	
				}

			  //密码
		      if (obj_json.password==true && !/^(?=.*[0-9].*)(?=.*[A-Za-z].*).{6,12}$/.test(value)){
		      		is_ok=set_warnning(obj,error_id,obj_json.error_msg);
		      		return is_ok;
		      }

		      //验证最小长度
		      if(obj_json.min_len!='undefined' &&value.length<obj_json.min_len) {
		      		is_ok=set_warnning(obj,error_id,obj_json.error_msg);
		      		 return is_ok;
		      }

	   		  //验证最大长度
		      if(obj_json.max_len!='undefined' && value.length>obj_json.max_len){
		      	is_ok=set_warnning(obj,error_id,obj_json.error_msg);
		      	return is_ok;
		      }

		      //手机号码
		      if(obj_json.mobile==true && !/^1[34578]\d{9}$/.test(value)){
		      		is_ok=set_warnning(obj,error_id,obj_json.error_msg);
		      		return is_ok;
		      }

		      //数字验证
		      if (obj_json.number==true && isNaN(value)){
		      		is_ok=set_warnning(obj,error_id,obj_json.error_msg);
		      		return is_ok;
		      }

		      //最小数字
		      if (obj_json.min_num!='undefined'&& Number(value)<=obj_json.min_num){

		      	   var msg="值必须大于"+obj_json.min_num;
		      		is_ok=set_warnning(obj,error_id,msg);
		      		return is_ok;
		      }
		      //最大数字
		      if (obj_json.max_num!='undefined'&& Number(value) > obj_json.max_num){
		    	  
		    	  var msg="值必须小于"+obj_json.max_num;
		    	  is_ok=set_warnning(obj,error_id,msg);
		    	  return is_ok;
		      }

		       //电话验证
		      if (obj_json.tel==true && !(/^([0-9]{3,4}-)?[0-9]{7,8}$/.test(value)
		      	                          ||/^1[34578]\d{9}$/.test(value))){
		      		is_ok=set_warnning(obj,error_id,obj_json.error_msg);
		      		return is_ok;
		      }

	     return true;
 }

function set_warnning(obj,error_id,error_msg){
	//obj.addClass('warnning');
   	obj.find("input[type=text]").each(function () {	//alert(error_id);
   				if($(this).val()==$(this).attr('title')||$(this).val()==''){
            		 $(this).addClass('warnning');   //alert(obj.attr('name')+1);
        		 }//alert(error_id);
        }); 

   if(obj.attr("type")=='text'||obj.prop("tagName")=="TEXTAREA"){
   		obj.addClass('warnning');   //alert(obj.attr('name')+2);
   	}
	
	//alert(error_msg+obj.attr('name'));
	//alert(obj.nextAll("span").eq(0).prop('outerHTML'));

	//if(obj.nextAll("span").eq(0).html()!='undefined'){
		//alert(2);
	//	obj.nextAll("span").eq(0).html(error_msg);
	//}
	//else{
			//obj.parent().nextAll("span").eq(0).html(error_msg);
	//}
	//alert(error_id+error_msg);
	if(error_id!="undefined"){//alert(error_id+error_msg);
	   $("#"+error_id).html(error_msg);
	 }
	//alert(obj.next('span').prop('outerHTML'));
	return false;
}