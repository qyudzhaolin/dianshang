

$(function(){
	$('#user_name').searchAll({
        url:'/m.php',
        interface:'Deal',
        method:'find_investor',
        fn:'fn1'
    });

});