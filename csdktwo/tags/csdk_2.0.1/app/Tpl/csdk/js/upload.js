function upd_file(obj,file_id,img_src,source,error_msg,type,x,y,max_size)
{	 
	$("input[name='"+file_id+"']").bind("change",function(){	
		$(obj).hide();
		$(obj).parent().find(".fileuploading").removeClass("hide");
		$(obj).parent().find(".fileuploading").removeClass("show");
		$(obj).parent().find(".fileuploading").addClass("show");
		$('#'+error_msg).html('正在上传');
		$('#'+error_msg).removeClass('gray');
		 $.ajaxFileUpload
		   (
			   {
				    url:APP_ROOT+'/upload_qiniu.php?file_name='+file_id+'&file_type='+type+'&x='+x+'&y='+y+'&max_size='+max_size,
				    secureuri:false,
				    fileElementId:file_id,
				    dataType: 'json',
				    success: function (data, status)
				    {
				   		$(obj).show();
				   		$(obj).parent().find(".fileuploading").removeClass("hide");
						$(obj).parent().find(".fileuploading").removeClass("show");
						$(obj).parent().find(".fileuploading").addClass("hide");
				   		if(data.status==1)
				   		{	$(document).find("iframe[name='jUploadFrame"+file_id+"']").remove();
				   			$(document).find("form[name='jUploadForm"+file_id+"']").remove();
				   			document.getElementById(source).value = data.key;
							data.url=data.url.replace(/&amp;/g,"&");
							if(file_id == 'bp'){
								var bp = document.getElementById(img_src);
								$('#'+error_msg).html('');
								bp.href=data.url;   //给img元素的src属性赋值
								bp.className='bp_see';
							}else{
								var bigImg = document.getElementById(img_src);
								bigImg.src=data.url;   //给img元素的src属性赋值
								$('.fileupload #img_src,.fileupload .no_img_src').css({'background-image':'none'}); 
								$('#'+error_msg).html('点击图片重新上传');
								$('#'+error_msg).addClass('gray');
								$("#personal_logo").attr('class', 'head_label img_border');
								$("#personal_logo_rw").attr('class', 'head_label img_border');
								$('#'+img_src).show();
								$('#'+img_src).removeClass("warnning");
							}
				   		}
						if(data.status!=1)
				   		{	
							$(document).find("iframe[name='jUploadFrame"+file_id+"']").remove();
				   			$(document).find("form[name='jUploadForm"+file_id+"']").remove();
							//alert(data.error);
							if(file_id == 'bp'){
								$('#'+error_msg).html(data.error);
							}else{
							$('#'+error_msg).html(data.error);}

				   		}
				   	
				    },
				    error: function (data, status, e)
				    {
						//alert('error');
				    	 
						$('#'+error_msg).html('上传失败');
						$('#'+img_src).addClass("warnning");
						$(obj).show();
				    }
			   }
		   );
		  $("input[name='"+file_id+"']").unbind("change");
	});	
}