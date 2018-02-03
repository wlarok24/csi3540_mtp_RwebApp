/*
	JavaScript file for the index page
*/
$(document).ready(function(){
	$("#signinJS").click(function(){
		var email = $("#email").val();
		var password = $("#password").val();
		if ((email == null)||(email === undefined)||(password == null)||(password === undefined)){
			swal("Error", "Please enter both your email and password.", "error");
		} else {
			$.ajax({
				url : "/api/accounts.php?op=signin",
				method : 'POST',
				data : {
					'login-email' : email,
					'login-password' : password
				},
				dataType : "json",
				cache : false,
				context : document.body,
				statusCode : {
					200 : function(data){ //Log in successful
						//Hide login modal
						$("[data-dismiss=modal]").trigger({ type: "click" });
						if($("#login-rememberme").is(':checked')){
							//Remember me is checked, thus I will store the data in localStorage
							localStorage.setItem("user_id", data.user_id);
							localStorage.setItem("user_email", data.user_email);
							localStorage.setItem("user_name", data.user_name);
							localStorage.setItem("user_token", data.user_token);
						} else {
							//Remember me is not checked, thus I will store the data in sessionStorage
							sessionStorage.setItem("user_id", data.user_id);
							sessionStorage.setItem("user_email", data.user_email);
							sessionStorage.setItem("user_name", data.user_name);
							sessionStorage.setItem("user_token", data.user_token);
						}
						$("#navbarDropdownMenuLink").html(data.user_name);
						$(".signed-in").show();
						$(".signed-out").hide();
					}
				},
				error : function(data){ //Log in unsuccessful
					//Hide login modal
					$("[data-dismiss=modal]").trigger({ type: "click" });
					swal("Error", "There was an error during the call.<br>" + data.responseJSON.message, "error");
				}
			});
		}
	});
});