$(function(){
    function loop_map(obj){
        //轮播图
        var oBox=$('#'+obj+'').get(0);
        var oPrev=oBox.getElementsByTagName('a')[0];
        var oNext=oBox.getElementsByTagName('a')[1];
        var oUl=oBox.getElementsByTagName('ul')[0];
        var aLi=oUl.children;
        var oOl=oBox.getElementsByTagName('ol')[0];
        var aBtn=oOl.children;
        var timer = null;
        var li_width=$('#'+obj+' ol li').eq(0).outerWidth()+28;
        var li_size = $('#'+obj+' ol li').size();
        $('#'+obj+' ol').css({'width':li_width*li_size,'marginLeft':-li_width*li_size/2+'px'})

        //复制一份
        oUl.innerHTML+=oUl.innerHTML;
        oUl.style.width=aLi.length*aLi[0].offsetWidth+'px';

        var W=oUl.offsetWidth/2;

        var iNow=0;


        oBox.onmouseover=function(){
            clearInterval(timer);
            oPrev.style.display='block';
            oNext.style.display='block';
        };
        oBox.onmouseout=function(){
            clearInterval(timer);
            timer=setInterval(function(){
                iNow++;
                tab();
            },3000)
            oPrev.style.display='none';
            oNext.style.display='none';
        };

        for(var i=0; i<aBtn.length; i++){
            (function(index){
                aBtn[i].onclick=function(){
                    iNow=index+Math.floor(iNow/aBtn.length)*aBtn.length;
                    tab();
                }
            })(i);
        }

        function tab(){
            for(var i=0; i<aBtn.length; i++){
                aBtn[i].className='';
            }
            if(iNow>0){
                aBtn[iNow%aBtn.length].className='active';
            }else{
                aBtn[(iNow%aBtn.length+aBtn.length)%aBtn.length].className='active';
            }
            //oUl.style.left=-iNow*aLi[0].offsetWidth+'px';
            startMove(oUl,-iNow*aLi[0].offsetWidth);
        }
        //启动轮播
        timer=setInterval(function(){
            iNow++;
            tab();
        },3000)

        oNext.onclick=function(){
            iNow++;
            tab();
        };
        oPrev.onclick=function(){
            iNow--;
            tab();
        };


        var left=0;
        function startMove(obj,iTarget){
            var count=Math.floor(1000/30);
            var start=left;
            var dis=iTarget-start;
            var n=0;
            clearInterval(obj.timer);
            obj.timer=setInterval(function(){
                n++;

                var a=1-n/count;
                left=start+dis*(1-Math.pow(a,3));

                if(left<0){
                    obj.style.left=left%W+'px';
                }else{
                    obj.style.left=(left%W-W)%W+'px';
                }

                if(n==count){
                    clearInterval(obj.timer);
                }
            },30);
        }
    }
    loop_map('index_box');


})
