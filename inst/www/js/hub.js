/*
	JavaScript file for the hub page
*/
var hub = hub || {};
hub.data = [];
hub.useData = [];
hub.predictionGraphData = [];
hub.refreshData = function(id, token){
	$.ajax({
		url : "/api/items.php?user_id=" + id + "&user_token=" + token,
		method : 'GET',
		cache : false,
		context : document.body,
		success : function(data){
			hub.data = data; //Refresh data
			hub.useData = new Array(data.length);
			$("#hub_table_body").html(""); //Empty the table
			for(var i = 0; i < data.length; i++){
				$("#hub_table_body").html($("#hub_table_body").html() + 
					"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-id=\"" + data[i].id + "\" data-name=\"" + data[i].name + "\">" +
					data[i].name + "</td><td>" + data[i].inventory + " " +  data[i].unit + "</td><td>" + data[i].usual_use_size + " " +  data[i].unit + 
					"</td><td><input type=\"number\" class=\"form-control bg-dark item_use\" id=\"item_use_" + data[i].id + "\" data-pos=\"" + i + "\"value=0></td></tr>"
				); //add table row for the item
				//Prep graph data
				var itemGraphData = [];
				var today = Date.now();
				for(var j = 0; j <= 14; j++){
					var y = Math.ceil(data[i].inventory/data[i].usual_use_size - data[i].model/data[i].usual_use_size*j);
					if(y < 0){y = 0;}
					itemGraphData.push([today + j * 24*60*60*1000, y]);
				}
				hub.predictionGraphData.push({
					label : data[i].name,
					data : itemGraphData,
					lines : {line : true, fill : false}
				});
				hub.getTodaysItemUse(id, token, data[i]);
			}
			//Draw graph
			$.plot($("#graphZone"), hub.predictionGraphData, {
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
		},
		error : function(data){
			swal("Error", "There was an error during the call.<br>" + data.message, "error");
		}
	});
};
hub.getTodaysItemUse = function(id, token, item){
	$.ajax({
		url : "/api/itemUse.php?user_id=" + id + "&user_token=" + token + "&op=today&item_id=" + item.id,
		method : 'GET',
		cache : false,
		context : document.body,
		success : function(data){
			var pos = $("#item_use_" + item.id).data("pos"); //Get position in Array
			if(data.length == 1){
				//Update item use
				$("#item_use_" + data[0].item_id).val(data[0].qty);
				hub.useData[pos] = data[0];
			} else {//No use data
				hub.useData[pos] = null;
			}
		},
		error : function(data){
			swal("Error", "There was an error during the call.<br>" + data.message, "error");
		}
	});
};
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
		hub.refreshData(id, token);
	} else {
		//Generate mock items
		$("#hub_table_body").html(""); //Empty the table
		$("#hub_table_body").html($("#hub_table_body").html() + 
			"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-id=\"potatoes\" data-name=\"potatoes\">" +
			"Potatoes</td><td>" + 30 + " units</td><td>" + 2 + " units" + 
			"</td><td><input type=\"number\" class=\"form-control bg-dark\" id=\"item_use_potatoes\"  value=0></td></tr>"
		); //add table row for the item
		$("#hub_table_body").html($("#hub_table_body").html() + 
			"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-id=\"yogurt\" data-name=\"yogurt\">" +
			"Yogurt</td><td>" + 2500 + " mL</td><td>" + 250 + " mL" + 
			"</td><td><input type=\"number\" class=\"form-control bg-dark\" id=\"item_use_yogurt\"  value=0></td></tr>"
		); //add table row for the item
		$("#hub_table_body").html($("#hub_table_body").html() + 
			"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-id=\"tp\" data-name=\"Toilet paper\">" +
			"Toilet paper</td><td>" + 48 + " units</td><td>" + 1 + " units" + 
			"</td><td><input type=\"number\" class=\"form-control bg-dark\" id=\"item_use_tp\"  value=0></td></tr>"
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
	$("#submit_use").click(function(){
		var itemsToSend = [];
		var promises = [];
		var allItems = $(".item_use").each(function(pos, item){//Iterate through the inputs
			var promise = $.Deferred();
			promises.push(promise);
			var sendUseData = false;
			var val = Number.parseInt(item.value);
			if(hub.useData[pos] == null){
				//No use data for today
				if(val > 0){
					if(!isNaN(val) && parseInt(Number(val)) == val && !isNaN(parseInt(val, 10))){
						//value is integer
						itemsToSend.push({
							item_id : hub.data[pos].id, 
							tracked_since : hub.data[pos].tracked_since,
							qty : val,
							update : false
						});
					}
				}
			} else {
				//There is use data for today
				if((val != hub.useData[pos].qty)&&(val >= 0)){
					// Value change
					if(!isNaN(val) && parseInt(Number(val)) == val && !isNaN(parseInt(val, 10))){
						//value is integer
						itemsToSend.push({
							item_id : hub.data[pos].id, 
							tracked_since : hub.data[pos].tracked_since,
							qty : val,
							update : true
						});
					}
				}
			}
			//Resolve promise
			promise.resolve();
		});
		//Only do this when all the each loops are done
		$.when.apply($, promises).done(function(){
			if((itemsToSend.length > 0)&&(!mockHub)){
				$.ajax({
					url : "/api/itemUse.php?user_id=" + id + "&user_token=" + token,
					method : 'POST',
					cache : false,
					context : document.body,
					data : {
						'items' : itemsToSend
					},
					dataType : "json",
					statusCode : {
						201 : function(){
							swal("Success", "Use data successfully added.", "success");
						}
					},
					error : function(data){
						swal("Error", "There was an error during the call.<br>" + data.message, "error");
					}
				});
			} else if(itemsToSend.length <= 0){
				swal("Error", "None of the values changed since your previous submit.", "error");
			}
		});
	});
});