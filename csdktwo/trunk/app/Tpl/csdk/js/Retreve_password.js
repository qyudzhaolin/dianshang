window.onload=function(){
    var oLine_div = document.querySelector('.spot_box div');
    var oI_2 = document.querySelector('.i_2');

    oLine_div.style.width = '190px';
    function end(fn){
        oLine_div.addEventListener('transitionend',fn,false);

    }
    end(function(){
        console.log(2)
        oI_2.style.color = '#fff';


        oI_2.addEventListener('transitionend',function(){
            oI_2.style.WebkitTransition = 'scale(1.3)';
            oI_2.style.MozTransition = 'scale(1.3)';
            oI_2.style.OTransform = 'scale(1.3)';
            oI_2.style.msTransform = 'scale(1.3)';
            oI_2.style.transform = 'scale(1.3)';
        },false);
    })







}