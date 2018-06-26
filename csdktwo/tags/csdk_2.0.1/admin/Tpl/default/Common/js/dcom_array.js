function del_event(o)
{
    //alert($(o).parent().html());
    //耦合性比较强 刘斯玉 2015 -7--29
	 
	
       if($(o).parent().prev().attr('data'))
          {

            $(o).parent().remove();
          }
      else
          {
            $(o).parent().find("input[type=text]").each(function () {
                var title=$(this).attr('title');//alert(title);
                if(title!=''){
                     $(this).val(title);
                     $(this).addClass("default_font");     
                      }
                 });
            $(o).parent().find("textarea[type=text]").each(function () {
                var title=$(this).attr('title');//alert(title);
                if(title!=''){
                     $(this).val(title);
                     $(this).addClass("default_font");     
                      }
                 });  

        $(o).parent().find("img").attr('src','');
        $(o).parent().find("img").attr('class','no_img_src');
        $(o).parent().find("input[type=hidden]").each(function(){
        	 
                 $(this).val('');
                   //$(o).parent().remove();
                });
        }

}

/*
tmpl_id:自定义属性名
max_size： 最多添加数量
 */

function add_dcom(tmpl_id,max_size)
{ 


    var len=0;
    var obj=null;

      var is_add=true;

    $("[data="+tmpl_id+"]").each(function () {
        len++;  
        obj=$(this); 
        //alert($(this).prop("data-url"));
        //禁止为空

        obj.find("input[type=text]").each(function () {
             if($(this).val()==''||$(this).val()==$(this).attr('title')){
                    $(this).addClass('warnning');
                    is_add=false;
             }
        }); 



   }); 

    if(!is_add){
            return;
      }
   
    if (len>=max_size&&max_size!=0) {
        alert("最多添加"+max_size+"条");
        return;
    }
     
      
     var o=obj.after(obj.prop('outerHTML'));
     
     o.next().find("input[type=text]").each(function(){
                   $(this).val($(this).attr('title'));
                });
     
     o.next().find("input[type=text]").removeClass("warnning");
     $("input[type=text],input[type=password],textarea").each(function(){
                    if($(this).val()==$(this).attr('title'))
                    {  
                       $(this).addClass("default_font"); 

                    } 
                });
     o.next().find("textarea[type=text]").each(function(){
                   $(this).val($(this).attr('title'));
                });
     
     o.next().find("textarea[type=text]").removeClass("warnning");
     $("input[type=text],input[type=password],textarea").each(function(){
                    if($(this).val()==$(this).attr('title'))
                    {  
                       $(this).addClass("default_font"); 

                    } 
                });

        o.next().find("input[type=hidden]").each(function(){
                   $(this).val('');
                });

		o.next().find("img").attr('src','');
        o.next().find("img").attr('class','no_img_src');

   //$('#create_time').load(APP_ROOT+'app/Tpl/csdk/add_create_time.html');
    //alert(html);
   //obj.after(obj);
    //var obj=$("#"+tmpl_id);
    //while(obj.next().attr('data')==tmpl_id)
    //{   
    //         alert(obj.next().attr('id'));
    //         obj=obj.next();
    //}

    //alert(obj.prop('outerHTML'));
   //obj.after(obj.prop('outerHTML'));
   //$("#"+parent_id).append(obj.prop('outerHTML'));
   // 
   //return;
   ////"/index.php?ctl=common&act=add_dcom&file="+file_name+"&len="+len, 
   /*  $.ajax({
        url: APP_ROOT+"/index.php?ctl=common&act=add_dcom&file="+file_name+"&len="+len,
        data: "ajax=1",
        success: function(obj){  
            $("#"+where_id).append(obj);
               
                $("input[type=text],input[type=password],textarea").each(function(){
                if($(this).val()==$(this).attr('title'))
                    {   
                       $(this).addClass("default_font"); 
                    } 
                });
      
        },
        error:function(obj)
        {
            alert("error"); 
        }
        });
    */
}