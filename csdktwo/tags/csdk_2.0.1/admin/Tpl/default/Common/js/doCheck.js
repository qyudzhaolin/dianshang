
function  doCheck(obj,limit,my_id){
	var iCount=0;
	var c=$("input[tag="+my_id+"]").size();
	for(var i=0;i<c;i++)   
	{   
	  	var chk = document.getElementById(my_id+i);   
	  	if(chk.checked){
	  		iCount++;  
	  	}   
	}
	if(iCount>limit){
	 obj.checked=false,c--;
	 alert("你已经选满"+limit+"条了。")
	}
}