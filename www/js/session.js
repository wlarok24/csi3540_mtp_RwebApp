/*
	JavaScript file to deal with a user that is connected or not
*/
var logout = function(){
	localStorage.removeItem("user_id");
	localStorage.removeItem("user_email");
	localStorage.removeItem("user_name");
	localStorage.removeItem("user_token");
	sessionStorage.removeItem("user_id");
	sessionStorage.removeItem("user_email");
	sessionStorage.removeItem("user_name");
	sessionStorage.removeItem("user_token");
	$(".signed-in").hide();
	$(".signed-out").show();
	if(window.location.href.indexOf("index") == -1){//Not on index
		window.location.replace("index.html"); //Go back to index
	}
};
$(document).ready(function(){
	if(sessionStorage.length > 0){
		//User is signed in
		$("#navMenuButton").html(sessionStorage.getItem("user_name"));
		$(".signed-in").show();
		$(".signed-out").hide();
	} else if (localStorage.length > 0){
		//User is signed in (with remember me)
		$("#navMenuButton").html(localStorage.getItem("user_name"));
		$(".signed-in").show();
		$(".signed-out").hide();
	} else {
		//User is signed out
		$(".signed-in").hide();
		$(".signed-out").show();
		if((window.location.href.indexOf("settings") != -1) || (window.location.href.indexOf("hub") != -1)){
			window.location.replace("index.html"); //Go back to index
		}
	}
	$("#signout").click(logout);
});