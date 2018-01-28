/*
	JavaScript file for the settings page
*/
$(document).ready(function(){
	var id, token;
	if (sessionStorage.length > 0){ 
		id = sessionStorage.getItem("user_id");
		token = sessionStorage.getItem("user_token");
	} else {
		id = localStorage.getItem("user_id");
		token = localStorage.getItem("user_token");
	}
	$("#submit_new_password").click(function(){
		
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
					swal("Success!", "You are signed up!", "success");
				}
			},
			error : function(data){ //Sign up unsuccessful
				swal("Error", "There was an error during the call.<br>" + data.message, "error");
			}
		});
	});
});