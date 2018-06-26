$(function(){
    function fn_tab(obj){
        var aBtn_text = document.getElementsByName(obj);
        var text = null;
        var arr = [];
        for(var i=0;i<aBtn_text.length;i++){
            arr.push(aBtn_text[i].value);
            aBtn_text[i].index = i;
            aBtn_text[i].onfocus = function(){
                text = aBtn_text[this.index].value;
                if(aBtn_text[this.index].value == arr[this.index]){
                    aBtn_text[this.index].value = '';
                }
                if(aBtn_text[this.index].innerHTML == arr[this.index]){
                    aBtn_text[this.index].innerHTML = '';
                    aBtn_text[this.index].style.color = '#000'
                }
            };
            aBtn_text[i].onblur = function(){
                if(aBtn_text[this.index].value == ''){
                    aBtn_text[this.index].value = arr[this.index];
                    //aBtn_text[this.index].style.color = '#cfcfcf';
                }
                if(aBtn_text[this.index].innerHTML == ''){
                    aBtn_text[this.index].innerHTML = arr[this.index];
                    aBtn_text[this.index].style.color = '#000';
                }

            }
        }
        //启发点
        //onblur="if(value==''){this.style.color='#b9b9b9';value='请输入公众号或者标签'}"
        //onfocus="if(value=='请输入公众号或者标签'){this.style.color='#333';value=''}"
        //value="请输入公众号或者标签" name=""
    }
    fn_tab('text1')
    fn_tab('textarea')
})