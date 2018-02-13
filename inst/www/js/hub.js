/*
	JavaScript file for the hub page
*/
var hub = hub || {};
hub.data = [];
hub.useData = [];
hub.predictionGraphData = [];
hub.consumptionGraphData = {
	item_id : "",
	data : {}
};
hub.drawGraph = function(data, minDate, maxDate){
	//Draw graph
	$.plot($("#graphZone"), data, {
		grid : {
			show : true,
			color : "#00ccff"
		}, xaxis : {
			mode : "time",
			min : minDate,
			max : maxDate,
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
hub.refreshData = function(id, token){
	$.ajax({
		url : "/api/items.php?user_id=" + id + "&user_token=" + token,
		method : 'GET',
		cache : false,
		context : document.body,
		success : function(data){
			hub.data = data; //Refresh data
			hub.useData = new Array(data.length);
			hub.predictionGraphData = [];
			$("#hub_table_body").empty(); //Empty the table
			$("#consumptionGraph-item").html("<option value=\"\"></value>"); //Reset item select
			for(var i = 0; i < data.length; i++){
				$("#hub_table_body").html($("#hub_table_body").html() + 
					"<tr class=\"hub_table_row\"><td><input class=\"form-check-input\" type=\"radio\" name=\"item_radio\" value=\"\" class=\"check_item\" data-pos=\"" + i + "\" data-id=\"" + data[i].id + "\" data-name=\"" + data[i].name + "\">" +
					data[i].name + "</td><td>" + data[i].inventory + " " +  data[i].unit + "</td><td>" + data[i].usual_use_size + " " +  data[i].unit + 
					"</td><td><input type=\"number\" class=\"form-control bg-dark item_use\" id=\"item_use_" + data[i].id + "\" data-pos=\"" + i + "\"value=0></td></tr>"
				); //add table row for the item
				$("#consumptionGraph-item").html($("#consumptionGraph-item").html() + "<option value=\"" + data[i].id + "\">" + data[i].name + "</option>");
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
			//Draw inventory graph
			$("#graph-tabs").find(".nav-link").removeClass("active");
			$("#consumptionGraph-toolbar").hide(); //Hide without animation
			$("#inventoryGraph").addClass("active");
			hub.drawGraph(hub.predictionGraphData, today, today + 14*24*60*60*1000);
		},
		error : function(data){
			swal("Error", "There was an error during the call.<br>" + data.message, "error");
		}
	});
};
hub.getTodaysItemUse = function(user_id, token, item){
	$.ajax({
		url : "/api/itemUse.php?user_id=" + user_id + "&user_token=" + token + "&op=today&item_id=" + item.id,
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
hub.getUseArchiveData = function(user_id, token, item_id, item_name){
	$.ajax({
		url : "/api/itemUse.php?user_id=" + user_id + "&user_token=" + token + "&op=archive&item_id=" + item_id,
		method : 'GET',
		cache : false,
		context : document.body,
		success : function(data){
			hub.consumptionGraphData.item_id = item_id;
			var graphData = [];
			for(var i = 0; i < data.length; i++){
				var date = data[i].date;
				var isodate = date.substr(4, 4) + '-' + date.substr(2, 2) + '-' + date.substr(0, 2);
				var jsDate = new Date(isodate);
				graphData.push([jsDate.getTime(), Number.parseInt(data[i].qty)]);
			}
			hub.consumptionGraphData.data = [{
				label : item_name,
				data : graphData,
				bars: { show: true }
			}];
			var today = Date.now();
			hub.drawGraph(hub.consumptionGraphData.data, today - 14*24*60*60*1000, today);
		},
		error : function(data){
			swal("Error", "There was an error during the call.<br>" + data.message, "error");
		}
	});
};
$(document).ready(function(){
	var id, token, mockHub = false, mockData;
	if (sessionStorage.length > 0){ 
		id = sessionStorage.getItem("user_id");
		token = sessionStorage.getItem("user_token");
		hub.refreshData(id, token);
	} else {
		id = localStorage.getItem("user_id");
		token = localStorage.getItem("user_token");
		hub.refreshData(id, token);
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
				 $.ajax({
					url : "/api/items.php?user_id=" + id + "&user_token=" + token + "&item_id=" + radio_checked.data("id"),
					method : 'DELETE',
					cache : false,
					context : document.body,
					statusCode : {
						204 : function(){ //Delete successful
							swal("Deleted!", "The item was successfully deleted!", "success").then(() => {
								hub.refreshData(id, token); //Refresh the data
							});
						}
					},
					error : function(){ //Delete unsuccessful
						swal("Error", "There was an error during the call.<br>" + data.message, "error");
					}
				 });
			  });
		} else {
			swal("Error", "Please check an item.", "error");
		}
	});
	// Change inventory
	$("#change_item_inventory").click(function(){
		var radio_checked = $("input[name='item_radio']:checked");
		if(radio_checked.length == 1){ //an item is checked
			$(".inventory-name").html(radio_checked.data("name"));
			$("#inventory-unit").html(hub.data[radio_checked.data("pos")].unit);
			$("#inventory-modal").modal('show');
		} else {
			swal("Error", "Please check an item.", "error");
		}
	});
	$("#inventory-update").click(function(){
		//inventory-qty
		var val = $("#inventory-qty").val();
		if(val == ""){
			swal("Error", "Please enter a quantity.", "error");
		} else if(isNaN(val)){
			swal("Error", "The inventory is not an integer.", "error");
		} else if (!Number.isInteger(Number.parseFloat(val))){
			swal("Error", "The inventory is not an integer.", "error");
		} else {
			var item_id = $("input[name='item_radio']:checked").data("id"); //Get id of checked item
			$.ajax({
				url : "/api/items.php?user_id=" + id + "&user_token=" + token + "&item_id=" + item_id + "&item_qty=" + val,
				method : 'PATCH',
				cache : false,
				context : document.body,
				statusCode : {
					200 : function(){
						swal("Success", "The inventory was updated!", "success").then(() => {
							hub.refreshData(id, token); //Refresh the data
							$("#inventory-modal").modal('hide');
						});
					}
				},
				error : function(data){
					swal("Error", "There was an error during the call.<br>" + data.message, "error");
				}
			});
		}
	});
	
	//Add item
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
		} else if (!Number.isInteger(Number.parseFloat($("#item-inventory").val())) || !Number.isInteger(Number.parseFloat($("#item-size").val()))){
			swal("Error", "The inventory or the item size are not integers.", "error");
		} else if ($("#item-estimate").val() <= 0 || $("#item-size").val() <= 0){
			swal("Error", "The usual use size and estimated daily use cannot be zero or negative.", "error");
		} else if ($("#item-inventory").val() < 0){
			swal("Error", "The inventory cannot be negative.", "error");
		} else {
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
							hub.refreshData(id, token); //Refresh the data
							$("#item-modal").modal('hide');
						});
					}
				},
				error : function(data){
					swal("Error", "There was an error during the call.<br>" + data.message, "error");
				}
			});
		}
	});
	$("#submit_use").click(function(){
		var itemsToSend = [];
		var promises = []; //Tableau qui va contenir tous les promesses (pour faire un appel AJAX)
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
						hub.useData[pos] = {
							item_id : hub.data[pos].id,
							qty : val
						}
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
							prev_qty : hub.useData[pos].qty,
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
							swal("Success", "Use data successfully added.", "success").then(() => {
								hub.refreshData(id, token); //Refresh the data
							});
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
	$("#inventoryGraph").click(function(){
		$("#graph-tabs").find(".nav-link").removeClass("active");
		$("#consumptionGraph-toolbar").hide(500); //Hide with animation
		$(this).addClass("active");
		//Redraw graph (next 14 days)
		var today = Date.now();
		hub.drawGraph(hub.predictionGraphData, today, today + 14*24*60*60*1000);
	});
	$("#consumptionGraph").click(function(){
		$("#graph-tabs").find(".nav-link").removeClass("active");
		$("#consumptionGraph-toolbar").show(500); //Show with animation
		$(this).addClass("active");
		//Redraw graph (past 14 days)
		var today = Date.now();
		hub.drawGraph(hub.consumptionGraphData.data, today - + 14*24*60*60*1000, today);
	});
	$("#consumptionGraph-draw").click(function(){
		var item_id = $("#consumptionGraph-item").val();
		var name = $("#consumptionGraph-item").find("option[value=" + item_id + "]").html();
		if (item_id == ""){
			swal("Warning", "You need to choose an item, before drawing the graph.", "warning");
		} else {
			hub.getUseArchiveData(id, token, item_id, name);
		}
	});
});