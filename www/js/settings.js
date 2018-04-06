/*
	JavaScript file for the settings page
*/
$(document).ready(function(){
	var id, token;
	if (sessionStorage.length > 0){ 
		id = sessionStorage.getItem("user_id");
		token = sessionStorage.getItem("user_token");
	} else if (localStorage.length > 0){
		id = localStorage.getItem("user_id");
		token = localStorage.getItem("user_token");
	}
	$("#submit_new_password").click(function(){
		if (($("#old_password").val() == "")||($("#new_password").val() == "")||($("#new_passwordRepeat").val() == "")){
			swal( "Error", "Please fill out all the fields.", "error");
			return;
		} else if ($("#new_password").val() != $("#new_passwordRepeat").val()){
			swal("Error", "The two new password fields don't match.", "error");
			return;
		}
		$.ajax({
			url : "/api/accounts.php?op=changepwd",
			method : 'POST',
			data : {
				'user_id' : id,
				'user_token' : token,
				'old_password' : $("#old_password").val(),
				'new_password' : $("#new_password").val(),
				'new_passwordRepeat' : $("#new_passwordRepeat").val()
			},
			dataType : "json",
			cache : false,
			context : document.body,
			statusCode : {
				200 : function(data){ //Sign up successful
					swal("Success!", "Password changed successfully!", "success");
				},
				401 : function(){
					swal("Error", "Your session is invalid.<br>You will be logged out.", "error").then(logout);
				}
			},
			error : function(data){ //Sign up unsuccessful
				swal("Error", "There was an error during the call.<br>Sorry for the inconvinence.", "error");
			}
		});
	});
});