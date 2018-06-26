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
			beforeSend: function () {
				$(obj).hide();
				$(obj).parent().find(".fileuploading").html('正在上传').removeClass('gray');
			},
			complete: function (XMLHttpRequest,status) {
				if(status == 'timeout'){// 超时,status还有success,error等值的情况
					ajaxTimeoutTest.abort();
					alert('网络超时！');
				}
			},
			success: function(data){
				
				if(data.status == 1){
					
					$.ajaxFileUpload({
						url: data.apiUrl,
						secureuri: false,
						fileElementId: file_id,
						dataType: 'json',
						data: { 'func' : data.func, 'params': data.params, 'signKey': data.signKey },
						success: function (fd, fs){
							if(fd.status == 0){
								file_obj = $(obj).children("input[type=file]");
								
								// obj.val
								file_obj.next().val(fd.response.key);
								
								if(fd.response.type == 2){
									// img
									var bpImg = file_obj.nextAll("img");
									bpImg.attr('href',fd.response.url);
									bpImg.addClass("bp_see");
									
									// message
									$(obj).parent().find(".fileuploading").html("").addClass('gray');
									
								}else{
									// img
									var bigImg 	= file_obj.nextAll("img");
									fd.response.url = fd.response.url.replace(/&amp;/g,"&");
									bigImg.attr('src',fd.response.url).css({'background-image':'none'}).show();
									
									// scale
//									file_obj.nextAll(".fileupload_scale").val(fd.response.scale);
									
									// message
									$(obj).parent().find(".fileuploading").html("点击图片重新上传").addClass('gray');
								
									// to user logo only
									$("#personal_logo").attr('class', 'head_label img_border');
								}
								
							}else{
								$(obj).parent().find(".fileuploading").html(fd.message).removeClass('gray');
							}
							
							$(obj).show();
						},
						error: function (){
							$(obj).show();
							$(obj).parent().find(".fileuploading").html("服务器出错了").removeClass('gray');
						}
					});
					
				}else{
					$(obj).show();
					$(obj).parent().find(".fileuploading").html(data.message).removeClass('gray');
				}
				
				file_obj.unbind("change");
			},
			error:function(){
				$(obj).show();
				$(obj).parent().find(".fileuploading").html('服务器异常').removeClass('gray');
				file_obj.unbind("change");
			}
		});
		
	});
}