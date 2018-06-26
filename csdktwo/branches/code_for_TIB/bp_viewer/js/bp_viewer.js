function bp_ajax()
{ 
        //1号线  
        myXmlHttpRequest = getXmlHttpObject(); 

        var key=$('key_value').value;
        var page_now=$('page_now_value').value;
        var page=parseInt(page_now);
        var page_next=page+1;
        var page_num=$('page_num_value').value;
        var page_num_int=parseInt(page_num);
        if(page_next>page_num_int){
            page_next=1;
         }
        //判断创建成功？  
        if(myXmlHttpRequest){  
            var url = "get_bp.php?src=check&key="+key+"&page="+page_next;
            myXmlHttpRequest.open("get",url,true);  
            myXmlHttpRequest.onreadystatechange = process;  
            myXmlHttpRequest.send(null);              
              
        }  
}
function bp_ajax_prev()
{ 
        //1号线  
        myXmlHttpRequest = getXmlHttpObject(); 

        var key=$('key_value').value;
        var page_now=$('page_now_value').value;
        var page=parseInt(page_now);
        var page_next=page-1;
        var page_num=$('page_num_value').value;
        var page_num_int=parseInt(page_num);
        if(page_next==0){
            page_next=page_num_int;
         }
        //判断创建成功？  
        if(myXmlHttpRequest){  
            var url = "get_bp.php?src=check&key="+key+"&page="+page_next;
            myXmlHttpRequest.open("get",url,true);  
            myXmlHttpRequest.onreadystatechange = process_prev;  
            myXmlHttpRequest.send(null);              
              
        }  
}
//创建ajax引擎  
 function getXmlHttpObject(){  
        var xmlHttpRequest;  
        if(window.ActiveXObject){  
            xmlHttpRequest = new ActiveXObject("Microsoft.XMLHTTP");  
        }else{  
            xmlHttpRequest = new XMLHttpRequest();  
        }  
        return xmlHttpRequest;  
}
 //回调函数  
function process(){  
        //window.alert("这是回调函数" + myXmlHttpRequest.readyState);            
        if(myXmlHttpRequest.readyState == 4){  
            //window.alert(myXmlHttpRequest.responseText);
            $('bp_url').src = myXmlHttpRequest.responseText;
            var page_now=$('page_now_value').value;
            var page=parseInt(page_now);
            var page_next=page+1;
            var page_num=$('page_num_value').value;
            var page_num_int=parseInt(page_num);
            if(page_next>page_num_int){
                page_next=1;
            }
            $('page_now_value').value = page_next;
            $('show_page').innerHTML = page_next;
        }  
          
    } 
//回调函数  
function process_prev(){  
        //window.alert("这是回调函数" + myXmlHttpRequest.readyState);            
        if(myXmlHttpRequest.readyState == 4){  
            //window.alert(myXmlHttpRequest.responseText);
            $('bp_url').src = myXmlHttpRequest.responseText;
            var page_now=$('page_now_value').value;
            var page=parseInt(page_now);
            var page_next=page-1;
            var page_num=$('page_num_value').value;
            var page_num_int=parseInt(page_num);
            if(page_next==0){
                page_next=page_num_int;
            }
            $('page_now_value').value = page_next;
            $('show_page').innerHTML = page_next;
        }  
          
    }   
      
function $(id){  
        return document.getElementById(id);  
}   