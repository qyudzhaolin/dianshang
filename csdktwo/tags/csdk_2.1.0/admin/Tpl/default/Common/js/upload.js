$(function(){ 
	$("#bp_real_url").click(function(event){ 
		event.stopPropagation(); 
	}); 
}); 
function upd_file(obj,type,x,y,max_size,scale)
{	 

	var file_obj=$(obj).find("input[type=file]");
	var file_id="file"+new Date().getTime();
	file_obj.attr('name',file_id);
	file_obj.attr('id',file_id);

	file_obj.unbind("change");

	file_obj.bind("change",function(){
		//var file_id=$(obj).find("input[type=file]").attr('name');
		$(obj).hide();
		$(obj).parent().find(".fileuploading").removeClass("hide");
		$(obj).parent().find(".fileuploading").removeClass("show");
		$(obj).parent().find(".fileuploading").addClass("show");
		$.ajaxFileUpload
		(
				{

					url:APP_ROOT+'/upload_qiniu.php?file_name='+file_id+'&file_type='+type+'&x='+x+'&y='+y+'&max_size='+max_size,
					secureuri:false,
					fileElementId:file_id,
					dataType: 'json',
					success: function (data, status)
					{

						file_obj=$(obj).find("input[type=file]");
						$(obj).show();
						$(obj).parent().find(".fileuploading").removeClass("hide");
						$(obj).parent().find(".fileuploading").removeClass("show");
						$(obj).parent().find(".fileuploading").addClass("hide");
						if(data.status==1)
						{	
							//document.getElementById(source).value = data.key;
							file_obj.next().val(data.key);
							data.url=data.url.replace(/&amp;/g,"&");
							if(data.type == 2){
								var bp = file_obj.next().next();//document.getElementById(img_src);
								bp.attr('src',bp.attr('_src'));
								bp.removeClass();
								bp.next().val(data.type);
//								bp.next().after('<a id="bp_real_url" target="_blank" href="'+data.url+'">点击查看</a>');
							}else{
								var bigImg=file_obj.next().next();//document.getElementById(img_src);
								bigImg.attr('src',data.url);
								bigImg.css({'background-image':'none'});
								$(obj).css({'background-image':'none'});
								if(scale){
									bigImg.next().val(data.scale);
								}else{
									bigImg.next().val(data.type);
								}
							}
						}
						if(data.status!=1)
						{	
							alert(data.error);
						}

					},
					error: function (data, status, e)
					{
						alert('error');
						$(obj).show();
						$(obj).parent().find(".fileuploading").removeClass("hide");
						$(obj).parent().find(".fileuploading").removeClass("show");
						$(obj).parent().find(".fileuploading").addClass("hide");
					}
				}
		);
		file_obj.unbind("change");
	});	
}