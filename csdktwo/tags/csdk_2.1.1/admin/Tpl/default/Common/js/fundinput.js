
$(function(){
    $('#user_name').searchAll({
        url:'/m.php?fund_id='+$("#fund_id").val(),
        interface:'Fund',
        method:'find_investor',
        fn:'fn3',
        beforeFn:function (){
        	$("#user_id").val("");
        	$("#user_mobile").val("");
        	$("#partner_user_id option").show();
        }
    });
});
