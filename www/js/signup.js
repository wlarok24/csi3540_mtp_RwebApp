/*
	JavaScript file for the signup page
*/
$(document).ready(function(){
	$("#signup").click(function(){
		if(!$("#signup_datawaiver").is(':checked')){
			swal("Error", "Please check the datawaiver.", "error");
			return;
		} else if (($("#signup_name").val() == "")||($("#signup_email").val() == "")||($("#signup_password").val() == "")
			||($("#signup_passwordRepeat").val() == "")){
			swal("Error", "Please fill out all the fields.", "error");
			return;
		} else if ($("#signup_password").val() != $("#signup_passwordRepeat").val()){
			swal("Error", "The two password fields don't match.", "error");
			return;
		}
		$.ajax({
			url : "/api/accounts.php?op=signup",
			method : 'POST',
			data : {
				'name' : $("#signup_name").val(),
				'email' : $("#signup_email").val(),
				'password' : $("#signup_password").val(),
				'passwordRepeat' : $("#signup_passwordRepeat").val()
			},
			dataType : "json",
			cache : false,
			context : document.body,
			statusCode : {
				201 : function(data){ //Sign up successful
					swal("Success!", "You are signed up!", "success");
				}
			},
			error : function(data){ //Sign up unsuccessful
				swal("Error", "There was an error during the call.<br>Sorry for the inconvinence.", "error");
			}
		});
	});
});