   //验证长度
  
   function company_save(){
    var company_save_json='[{"name":"company_name","required":true,"error_id":"company_name_msg","error_msg":"请填写完整"},\
                             {"name":"company_title","required":true,"error_id":"company_title_msg","error_msg":"请填写完整"},\
                             {"name":"user_name","max_len":200,"error_id":"user_name_msg","error_msg":"请确保内容在200个字以内"}]';
       var res=csdk_check(company_save_json);
        if (!res) {
          $('input[type=text]').css({'border':'1px solid red'})
           return false;
       }else{
          var query = new Object();  
        query.company_name=document.getElementsByName('company_name')[0].value;
        query.company_title=document.getElementsByName('company_title')[0].value;
        query.com_legal=document.getElementsByName('com_legal')[0].value;
        query.com_tel=document.getElementsByName('com_tel')[0].value;
        query.com_web=document.getElementsByName('com_web')[0].value;
        query.company_brief=document.getElementsByName('company_brief')[0].value;
        $.ajax({ 
        url: APP_ROOT+"/index.php?ctl=investor&act=company_update",
        dataType: "json",
        data:query,
        type: "POST",
        success: function(obj){
            if(obj.status==2)
            { 
              alert(2);
       
            }
            else
            {
             $('#error_msg').html(obj.info);
            }
        },
        error:function(obj)
        {
            $('#error_msg').html('操作失败');
        }
        });
       }
        
    

}