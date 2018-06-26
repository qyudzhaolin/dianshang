$(function(){

    //$('body').css({
    //    'width':$(window).width(),
    //    'height':$(window).height()
    //
    //})
    $('.box_min').height($(window).height());
    var num = 0;
    var arr = ['images/1.png','images/2.png','images/3.png','images/4.png','images/5.png'];
    for(var i=0;i<arr.length;i++){
        var iMg = new Image();
        iMg.src = arr[i];
        iMg.onload=function(){
            num++;
            if(num == 4){
                a();
            }
        }
    }
    function a(){

        $('.box_min').css('zIndex','0');
        $('.index2').css('zIndex','1');
        //
        //alert(1)
    }
})