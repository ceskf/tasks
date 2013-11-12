$(function(){ 
	$('#auth').modal();
	$("#enter").bind('click', function(){
		var user = $("#login").val();
		var pwd = $("#pwd").val();
		check_user(user,pwd);
	});
})

function check_user(login, pwd){
	$("#loader").css({'display':'block'});
	$("#error").css({'display':'none'});
    var params = {
        user : login,
        password: pwd,
        type:'wallpapers'
    }
    $.ajax({
        url: "ajax/users.php",
        type: "POST",
        async: false,
        dataType: "json",   
        data: {params : params, type : 'check'},
        success: function(data){
            if (data == 1){
            	$("#loader").css({'display':'none'});
            	document.location.href = "index.php";
            }
            else{
            	$("#loader").css({'display':'none'});
            	$("#error").css({'display':'block'});
            }
        },
        error: function(){
        	$("#loader").css({'display':'none'});
        	$("#error").css({'display':'block'});
        }
    });
}