// 本js文件主要用于实现联想搜索
// 要求输入联想文本框及选择联想值后需要填充的两个表单标签的ID，另需要传入ajax请求方法名称
// 另外ajax返回的参数名需分别为id,name,与third_data;
function getLinkData(linkInputId,moudleName,actionName,otherInput1,otherInput2) {
    var popupDiv = document.getElementById("popup");//获得对应的div对象
    var popupBody = document.getElementById("popupBody");//获得对应的tbody对象
    var linkDataProperty = document.getElementById(linkInputId); //获得对应的输入框对象
    clearModels();//清除联想输入提示框的内容
    //利用ajax获取后台的模糊查询的数据，并且封装成下拉列表的形式展现出来
    $.ajax({
        // type : "post",//提交的方法为post
        url:ROOT+"?"+VAR_MODULE+"="+moudleName+"&"+VAR_ACTION+"="+actionName+"&linkValue="+linkDataProperty.value,//对应的moudel方法
        data:"ajax=1",//从前台传递到后台的查询语句的参数
        dataType : "json",  //从Action中返回的数据的类型为json类型的
        error:function(){
            alert("没有对应的数据，请查看输入的查询条件！");
        },
        success : function(obj) { //当Ajax提交成功时调用的方法
        		   var status = obj.status;
        		   var data = obj.data;
        		   if(status == 0 || data == null){
        			   alert("没有查到相应数据");
        			   return;
        		   }
                  setOffsets();//设置联想输入下拉列表提示框的位置
                  var tr,td,text;
                  for (var i = 0; i < data.length; i++) {//根据返回的值，手动的拼tbody的内容
                  text = document.createTextNode(data[i].name);//从Action中返回的数据中取出linkDataProperty的值
                  hiddenInput=document.createElement("input");
                  hiddenInput.setAttribute("id","data" + data[i].id);
                  hiddenInput.setAttribute("type","hidden");
                  hiddenInput.setAttribute("value",data[i].id +"!!"+data[i].third_data);
                  td = document.createElement("td");//创建一个td的对象           
                  tr = document.createElement("tr");//创建一个tr的对象           
                  td.mouseOver = function(){this.className="mouseOver;"};
                  td.mouseOut = function(){this.className="mouseOut;"};
                  td.onclick = function(){populateModel(this,otherInput1,otherInput2)};//单击td是的方法为populateModel             
                  td.appendChild(text);
                  td.appendChild(hiddenInput);
                  tr.appendChild(td);            
                  popupBody.appendChild(tr);
              }
        }});
    //点击下拉列表中的某个选项时调用的方法
    function populateModel(cell,otherInput1,otherInput2) {
            // clearSelect(); // 暂没发现用处
            linkDataProperty.value = cell.firstChild.nodeValue; 
            var hiddenValue = $(cell).find('input').val();
            initOtherData(hiddenValue,otherInput1,otherInput2); // 利用输入框中的数据调用其他方法，初始化其他数据
            clearModels();//清除自动完成行                        
    }
    
    function initOtherData(hiddenValue){
    	strs = hiddenValue.split("!!");
    	$("#"+otherInput1).val(strs[0]);
    	$("#"+otherInput2).val(strs[1]);
    }
    
    //清除自动完成行，只要tbody有子节点就删除掉，并且将将外围的div的边框属性设置为不可见的
    function clearModels() {
        while (popupBody.childNodes.length > 0) {
            popupBody.removeChild(popupBody.firstChild);
        }
        popupDiv.style.border = "none";
    }
    //设置下拉列表的位置和样式
    function setOffsets() {
        var width = linkDataProperty.offsetWidth;//获取linkDataProperty输入框的相对宽度
        var left = getLeft(linkDataProperty);
        var top = getTop(linkDataProperty) + linkDataProperty.offsetHeight;

        popupDiv.style.border = "black 1px solid";
        popupDiv.style.left = left + "px";
        popupDiv.style.top = top + "px";
        popupDiv.style.width = width + "px";
    }
    //获取指定元素在页面中的宽度起始位置
    function getLeft(e) {
        var offset = e.offsetLeft;
        if (e.offsetParent != null) {
            offset += getLeft(e.offsetParent);
        }
        return offset;
    }
    //获取指定元素在页面中的高度起始位置
    function getTop(e) {
        var offset = e.offsetTop;
        if (e.offsetParent != null) {
            offset += getTop(e.offsetParent);
        }
        return offset;
    }
}

  //清空输入框中的数据
function clearSelect() {
    var linkDataProperty=document.getElementById(linkDataProperty);
    linkDataProperty.val("");
}