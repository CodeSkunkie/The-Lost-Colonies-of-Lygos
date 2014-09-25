
//Inbox or Sent? variable
var inbox=true;
//Populate messages
get_messages();
//Generate message composer
generate_message_composer();
//Check for new messages every 60 seconds
setInterval(function(){get_messages()}, 60000);
// This array will get populated with user id's and usernames. id's are the indexes.
var usernames = [];


// This function is called when someone clicks the maximize button
// on the messaging mini div. It changes the main screen to the
// messaging screen and it gets new messages from the database
function maximize_messages () {
	// Grab the name of this screen
	var name = 'messaging';
	change_screen(name);
	get_messages();
	
}

// This function is called every 60 seconds and gets new messages
// from the database
function get_messages () { 
	// Grab the name of this screen
	var name = 'messaging';
	// Call the data-fetching script for this screen.
	request_data('game_screen_' + name, function(json_data) {
		// Data was successfully fetched.
		
		// Clear the content from the mini div
		$('#message_display_table_mini tr').remove();
		// Clear the content from the inbox div
		$('#message_display_container div').remove();
		
		usernames = json_data.usernames;
		
		// Iterate through the messages to
		// populate the content of the mini div
		for ( var i in json_data.messages )
		{
			var message = json_data.messages[i];
			console.log(message);
			var message_class = "";
			if(message.viewed==1){
				message_class="message_viewed";
			}else if(message.viewed==0){
				message_class="message_unviewed";
			}
			
			$('<tr>', {
				"class":'message_display_row',
			}).appendTo('#message_display_table_mini')
			.append(
				$('<td>', {
					"class":'message_display_col',
					"id":'title_mini'
				}).text("FROM"),
				$('<td>', {
					"class":'message_display_col',
					"id":'content_mini'
				}).text(usernames[message.from_player]) 
			);
			
			$('<tr>', {
				"class":'message_display_row',
			}).appendTo('#message_display_table_mini')
			.append(
				$('<td>', {
					"class":'message_display_col',
					"id":'title_mini'
				}).text("SUBJECT"),
				$('<td>', {
					"class":'message_display_col',
					"id":'content_mini'
				}).text(message.subject)
			);
			
				$('<tr>', {
					"class":'message_display_row_spacer',
				}).appendTo('#message_display_table_mini')
			
			if(inbox){	
				$('<div>', {
					"class":message_class,
					"id":'message'+message.id,
					"onclick":'javascript:message_click('+message.id+');'
				}).appendTo('#message_display_container')
					.text(usernames[message.from_player]+ " sent you a message about \""
					+message.subject+"\" saying \""
					+message.message.substring(0,22)+"...\"");
			}
		}
		for( var i in json_data.messages_sent){
			var message = json_data.messages_sent[i];
			if(!inbox){
				$('<div>', {
					"class":'message_viewed',
					"id":'message'+message.id,
					"onclick":'javascript:message_click('+message.id+');'
				}).appendTo('#message_display_container')
					.text("You sent "+ usernames[message.to_player]+" a message about \""
					+message.subject+"\" saying \""
					+message.message.substring(0,22)+"...\"");
			}
			
			
		}
	});
}

//The two functions below switch the message container between
//displaying the inbox and the sent messages by toggling a
//boolean value. They have corresponding buttons on the main
//messages div
function display_inbox(){
	inbox=true;
	get_messages();
}
function display_sent(){
	inbox=false;
	get_messages();
}




//The following function is called when a message is clicked
//The specific message will be pulled up in the message viewing div
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


//This function is called when the page loads
//It populates the messaging screen with the 
//necessary elements to compose a message
function generate_message_composer(){
	$('#message_composer').empty();
	
	$('<p>', {
		"id":'to_title',
	}).appendTo('#message_composer')
		.text("Send to Player:");
	$('<input>', {
		"id":'to_field',
		"type":'text'
	}).appendTo('#message_composer');
	$('<p>', {
		"id":'subject_title',
	}).appendTo('#message_composer')
		.text("Subject:");
	$('<input>', {
		"id":'subject_field',
		"type":'text',
		"maxlength":'20'
	}).appendTo('#message_composer');
	$('<textarea>', {
		"id":'message_field'
	}).appendTo('#message_composer');
	$('<input>', {
		"id":'message_submit',
		"type":'submit',
		"value":'Send Message'
	}).appendTo('#message_composer');
	
}

//This function submits the form
$('#message_submit').click(function() {
	
	var to = $("#to_field").val();
	var message = $("#message_field").val();
	var subject = $("#subject_field").val();
	var viewed = 0;
	// Returns successful data submission message when the entered information is stored in database.
	var form_object = {"to1": to , "message1": message , "subject1": subject};
	if(to=='' )
		alert("You must enter a recipient.");
	else if (message=='')
		alert("You must enter a message.");
	else if (subject=='')
		alert("You must enter a subject.");
	else {
		request_data('message_submit', form_object , function(json_data){
			if ( json_data.WARNING == 'unknown_username' )
				alert("The username you entered does belong to any player on this server.");
			else
				generate_message_composer();
		});
	}
});


//What happens when a message is clicked
//function message_click(message_id){
	
//}

//Set messages as read when you click on them
function message_click(message_id){
	var viewed = 1;
	request_data('message_read', {"viewed1":viewed,"id1":message_id},function(json_data){	
		go_to_message(message_id);
		get_messages();
	});
};

