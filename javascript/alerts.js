
//Populate alerts
get_alerts();
//Check for new messages every 60 seconds
setInterval(function(){get_alerts()}, 60000);


// This function is called when someone clicks the maximize button
// on the alerts mini div. It changes the main screen to the
// alerts screen and it gets new messages from the database
function maximize_alerts () {
	// Grab the name of this screen
	var name = 'alerts';
	change_screen(name);
	get_alerts();
	
}

// This function is called every 60 seconds and gets new alerts
// from the database
function get_alerts () { 
	// Grab the name of this screen
	var name = 'alerts';
	// Call the data-fetching script for this screen.
	request_data('game_screen_' + name, function(json_data) {
		// Data was successfully fetched.
		
		// Clear the content from the mini div
		$('#alerts_list_mini').empty();
		// Clear the content from the main div
		$('#alerts_list').empty();
				
		// Iterate through the alerts to
		// populate the content of the main div
		// and the mini div
		for ( var i in json_data.alerts )
		{
			var alert = json_data.alerts[i];
			//console.log(message);
			
			//configure div class by extracting type
			var alert_class = "";
			var alert_img = "";
			if(alert.alert_type==0){
				alert_class="battle_report";
				alert_img="battle_alert.png";
				alert_title="Battle Report";
			}else if(alert.alert_type==1){
				//extend functionality here...
			}
			
			$('<div>', {
				"class":'alert_containter',
				"id":'alert_container'+alert.id,
				"height":'56px',
				"width":'90%',
				"overflow":'auto'
			}).appendTo('#alerts_list');
			
			$('<img>', {
				"class":'alert_img',
				"src":'media/themes/default/images/'+alert_img+''
			}).appendTo('#alert_container'+alert.id);
			
			$('<div>', {
				"class":alert_class,
				"id":alert_class+''+alert.id,
				"onclick":'javascript:expand_alert('+alert.id+');'
			}).appendTo('#alert_container'+alert.id)
				.text(" [+] "+alert_title);
			
			$('<div>', {
				"class":'alert_contents',
				"id":'alert_contents'+alert.id,
				"height":'30px',
				"width":'80%',
				"visibility":'hidden',
				"onclick":'javascript:contract_alert('+alert.id+');'
			}).appendTo('#alert_container'+alert.id)
				.text(" [-] "+alert.alert_contents);
			
			//mini div
			$('<div>', {
				"class":alert_class+'_mini',
				"onclick":'javascript:maximize_alerts();'
			}).appendTo('#alerts_list_mini')
				.text(alert_title);
			
		}
		
	});
}


/*
//TODO:
//The following function is called when an alert is clicked
//The specific message will be expanded and marked as read

function go_to_message(message_id){
	//clear any previous message
	$('#message_viewer div').remove();
	// Grab the name of this screen
	var name = 'messaging';
	// Call the data-fetching script for this screen.
	request_data('game_screen_' + name, function(json_data) {
		// If data was successfully fetched...
		if ( json_data.ERROR == "" )
		{
			//console.log(json_data);
	
			for( var i in json_data.messages){
				var message = json_data.messages[i];
				if(message.id==message_id){
					$('<div>',{
						"id":'message_viewer_subject'
					}).appendTo('#message_viewer')
						.text("SUBJECT: "+message.subject);
					$('<div>',{
						"id":'message_viewer_from'
					}).appendTo('#message_viewer')
						.text("FROM: "+ usernames[message.from_player]);
					$('<div>',{
						"id":'message_viewer_message'
					}).appendTo('#message_viewer')
						.text(message.message);
				}
			}
			for( var i in json_data.messages_sent){
				var message = json_data.messages_sent[i];
				if(message.id==message_id){
					$('<div>',{
						"id":'message_viewer_subject'
					}).appendTo('#message_viewer')
						.text("SUBJECT: "+message.subject);
					$('<div>',{
						"id":'message_viewer_from'
					}).appendTo('#message_viewer')
						.text("FROM: "+ player_username);
					$('<div>',{
						"id":'message_viewer_message'
					}).appendTo('#message_viewer')
						.text(message.message);
				}
			}
		}
		else
			alert(json_data.ERROR);
	});
	
}

*/


function expand_alert(alert_id){
	$("#alert_contents"+alert_id).show();	
}

function contract_alert(alert_id){
	$("#alert_contents"+alert_id).hide();
}

//TODO: Set alerts as read when you click on them
/*
function alert_click(alert_id){
	var viewed = 1;
	request_data('message_read', {"viewed1":viewed,"id1":message_id},function(json_data){	
		//go_to_message(message_id);
		get_alerts();
	});
};
*/