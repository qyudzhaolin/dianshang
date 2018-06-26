function upd_file(obj,type,x,y,max_size,scale){
	var file_obj 	= $(obj).find("input[type=file]");
	var file_id 	= "file"+new Date().getTime();
	file_obj.attr('name',file_id);
	file_obj.attr('id',file_id);
	
	file_obj.unbind("change");
	
	x = x || 0;
	y = y || 0;
	max_size 	= max_size || 0;
	scale 		= scale || 0;
	
	file_obj.bind("change",function(){
		
		var ajaxTimeoutTest = $.ajax({
			url: APP_ROOT+"/upload_qiniu.php",
			timeout: 20000,
			dataType: "json",
			data: "file_name="+file_id+"&file_type="+type+"&x="+x+"&y="+y+"&max_size="+max_size+"&scale="+scale,
			type: "POST",
			complete: function (XMLHttpRequest,status) {
				if(status == 'timeout'){// 超时,status还有success,error等值的情况
					ajaxTimeoutTest.abort();
					alert('网络超时');
				}
				file_obj.unbind("change");
			},
			success: function(data){
				
				if(data.status == 1){
					$(obj).hide();
					$(obj).parent().find(".fileuploading").show();
					
					$.ajaxFileUpload({
						url: data.apiUrl,
						secureuri: false,
						fileElementId: file_id,
						dataType: 'json',
						data: { 'func' : data.func, 'params': data.params, 'signKey': data.signKey },
						success: function (fd, fs){
							$(obj).show();
							$(obj).parent().find(".fileuploading").hide();
							
							if(fd.status == 0){
								file_obj = $(obj).children("input[type=file]");
								
								// obj.val
								file_obj.next().val(fd.response.key);
								
								if(fd.response.type == 2){
									// img
									var bpImg = file_obj.nextAll("img");
									bpImg.attr('src',bpImg.attr('_src'));
									
									// type
									file_obj.nextAll(".fileupload_type").val(fd.response.type);
									
								}else{
									// img
									var bigImg 	= file_obj.nextAll("img");
									fd.response.url = fd.response.url.replace(/&amp;/g,"&");
									bigImg.attr('src',fd.response.url).css({'background-image':'none'});
									$(obj).css({'background-image':'none'});
									
									// scale
									file_obj.nextAll(".fileupload_scale").val(fd.response.scale);
									
									// type
									file_obj.nextAll(".fileupload_type").val(fd.response.type);
								}
								
							}else{
								alert(fd.message);
							}
						},
						error: function (){
							alert('上传失败error');
							$(obj).show();
							$(obj).parent().find(".fileuploading").hide();
						}
					});
					
				}else{
					alert(data.message);
				}
				
				file_obj.unbind("change");
			},
			error:function(){
				alert('获取上传加密参数失败');
			}
		});
		
	});
}