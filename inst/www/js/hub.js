/*
	JavaScript file for the hub page
*/
$(document).ready(function(){
	var id, token, mockHub = false, mockData;
	if(window.location.href.indexOf("csi3540_mtp_RwebApp/inst/www") != -1){
		//Offline (For mockups)
		mockHub = true;
		var potatoes =[], yogurt = [], tp = [];
		var today = Date.now();
		for(var i = 0; i <= 14; i++){
			potatoes.push([today + i * 24*60*60*1000, Math.ceil(15 - 5/6*i)]);
			yogurt.push([today + i * 24*60*60*1000, Math.ceil(10 - 0.5*i)]);
			tp.push([today + i * 24*60*60*1000, Math.ceil(48 - 0.25 * i)]);
		}
		mockData = [{
				label : "Potatoes",
				data : potatoes,
				lines : {line : true, fill : false}
			},{
				label : "Yogurt",
				data : yogurt,
				lines : {line : true, fill : false}
			}, {
				label : "Toilet paper",
				data : tp,
				lines : {line : true, fill : false}
			}];
	} else if (sessionStorage.length > 0){ 
		id = sessionStorage.getItem("user_id");
		token = sessionStorage.getItem("user_token");
	} else {
		id = localStorage.getItem("user_id");
		token = localStorage.getItem("user_token");
	}
	if(!mockHub){
	 $.ajax({
		url : "/api/items.php?user_id=" + id + "&user_token=" + token,
		method : 'GET',
		cache : false,
		context : document.body,
		success : function(data){
			$("#hub_table_body").html(""); //Empty the table
			for(var i = 0; i < data.length; i++){
				$("#hub_table_body").html($("#hub_table_body").html() + 
					"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-id=\"" + data[i].id + "\" data-name=\"" + data[i].name + "\">" +
					data[i].name + "</td><td>" + data[i].inventory + " " +  data[i].unit + "</td><td>" + data[i].usual_use_size + " " +  data[i].unit + 
					"</td><td><input type=\"number\" class=\"form-control bg-dark\" id=\"item_use_" + data[i].id + "\"  value=0 required></td></tr>"
				); //add table row for the item
			}
		},
		error : function(data){
			swal("Error", "There was an error during the call.<br>" + data.message, "error");
		}
	 });
	} else {
		//Generate mock items
		$("#hub_table_body").html(""); //Empty the table
		$("#hub_table_body").html($("#hub_table_body").html() + 
			"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-id=\"potatoes\" data-name=\"potatoes\">" +
			"Potatoes</td><td>" + 30 + " units</td><td>" + 2 + " units" + 
			"</td><td><input type=\"number\" class=\"form-control bg-dark\" id=\"item_use_potatoes\"  value=0 required></td></tr>"
		); //add table row for the item
		$("#hub_table_body").html($("#hub_table_body").html() + 
			"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-id=\"yogurt\" data-name=\"yogurt\">" +
			"Yogurt</td><td>" + 2500 + " mL</td><td>" + 250 + " mL" + 
			"</td><td><input type=\"number\" class=\"form-control bg-dark\" id=\"item_use_yogurt\"  value=0 required></td></tr>"
		); //add table row for the item
		$("#hub_table_body").html($("#hub_table_body").html() + 
			"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-id=\"tp\" data-name=\"Toilet paper\">" +
			"Toilet paper</td><td>" + 48 + " units</td><td>" + 1 + " units" + 
			"</td><td><input type=\"number\" class=\"form-control bg-dark\" id=\"item_use_tp\"  value=0 required></td></tr>"
		); //add table row for the item
		//Draw mock graphs
		$.plot($("#graphZone"), mockData, {
			grid : {
				show : true,
				color : "#00ccff"
			}, xaxis : {
				mode : "time",
				min : today,
				max : today + 14*24*60*60*1000,
				font : {
					color : "#ffffff"
				}
			}, yaxis : {
				min : 0,
				font : {
					color : "#ffffff"
				}
			}, legend : { 
				show : true, 
				sorted : "ascending",
				position : "sw",
				backgroundOpacity : 0.5
			}
		});
	}
	$("#remove_item").click(function(){
		var radio_checked = $("input[name='item_radio']:checked");
		if(radio_checked.length == 1){ //an item is checked
			swal({
			  title: 'Remove ' + radio_checked.data("name"),
			  confirmButtonText: 'Yes',
			  showCancelButton: true,
			  cancelButtonText: 'No',
			  text:
				"Are you sure you want to remove " + radio_checked.data("name")+ "?",
			  showLoaderOnConfirm: true
			}).then(() => {//Confirm
				if(!mockHub){
				 $.ajax({
					url : "/api/items.php?user_id=" + id + "&user_token=" + token + "&item_id=" + radio_checked.data("id"),
					method : 'DELETE',
					cache : false,
					context : document.body,
					statusCode : {
						204 : function(){ //Delete successful
							swal("Deleted!", "The item was successfully deleted!", "success").then(() => {
								window.location.reload(true); //Force reload of page
							});
						}
					},
					error : function(){ //Delete unsuccessful
						swal("Error", "There was an error during the call.<br>" + data.message, "error");
					}
				 });
				}
			  });
		} else {
			swal("Error", "Please check an item.", "error");
		}
	});
	$("#add-item").click(function(){//Click handler for add-item
		//Check input validity
		if($("#item-name").val() == "" || $("#item-unit").val() == "" || $("#item-size").val() == ""
			|| $("#item-inventory").val() == "" || $("#item-estimate").val() == ""){
			swal("Error", "Please fill out all the fields", "error");
		} else if ($("#item-estimate").val().indexOf(',') !== -1 || $("#item-size").val().indexOf(',') !== -1
			|| $("#item-inventory").val().indexOf(',') !== -1){//No commas allowed in any number field
			swal("Error", "No commas are allowed in number fields.", "error");
		} else if (isNaN($("#item-estimate").val())||isNaN($("#item-size").val())
			|| isNaN($("#item-inventory").val())){//Number field need to be numbers
			swal("Error", "Number field(s) inputs are not numbers.", "error");
		} else if (Number.isInteger($("#item-inventory").val()) || Number.isInteger($("#item-size").val())){
			swal("Error", "The usual use size and estimated daily use cannot be zero or negative.", "error");
		} else if ($("#item-estimate").val() <= 0 || $("#item-size").val() <= 0){
			swal("Error", "The usual use size and estimated daily use cannot be zero or negative.", "error");
		} else if ($("#item-inventory").val() < 0){
			swal("Error", "The usual use size and estimated daily use cannot be negative.", "error");
		} else {
			if(!mockHub){
			//Make ajax call to hubAjaxHandler
			$.ajax({
				url : "/api/items.php?user_id=" + id + "&user_token=" + token,
				method : 'POST',
				cache : false,
				context : document.body,
				data : {
					'name' : $("#item-name").val(),
					'unit' : $("#item-unit").val(),
					'usual_use_size' : $("#item-size").val(),
					'inventory' : $("#item-inventory").val(),
					'estimated_daily_use' : $("#item-estimate").val()
				},
				dataType : "json",
				statusCode : {
					201 : function(){
						swal("Success", "Item successfully added.", "success").then(() => {
							window.location.reload(true); //Force reload of page
						});
					}
				},
				error : function(data){
					swal("Error", "There was an error during the call.<br>" + data.message, "error");
				}
			});
			}
		}
	});
});