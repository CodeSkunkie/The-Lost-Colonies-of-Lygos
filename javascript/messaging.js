
//Inbox or Sent? variable
var inbox=true;
//Populate messages
get_messages();
//Generate message composer
generate_message_composer();
//Check for new messages every 60 seconds
setInterval(function(){get_messages()}, 60000);


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
		// If data was successfully fetched...
		if ( json_data.ERROR == "" )
		{
			//console.log(json_data);
			
			
			//Iterate through the messages
			// Clear the content from the mini div
			$('#message_display_table_mini tr').remove();
			// Clear the content from the inbox div
			$('#message_display_container div').remove();
			
			// Populate the content of the mini div
			for ( var i in json_data.messages )
			{
				var message = json_data.messages[i];
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
					}).text(message.from_player) //TODO: Convert this id into the actual player name
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
						.text("Player "+ message.from_player+" sent you a message about \""
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
						.text("You sent Player "+ message.to_player+" a message about \""
						+message.subject+"\" saying \""
						+message.message.substring(0,22)+"...\"");
				}
				
				
			}
			
			
			
			
		}
		else
			alert(json_data.ERROR);
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
						.text("FROM: Player "+message.from_player);
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
						.text("FROM: Player "+message.from_player);
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
		.text("Send to Player ID:");
	$('<input>', {
		"id":'to_field',
		"type":'number',
		"maxlength":'5'
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
	
	var from = player_id;
	var to = $("#to_field").val();
	var message = $("#message_field").val();
	var subject = $("#subject_field").val();
	var viewed = 0;
	// Returns successful data submission message when the entered information is stored in database.
	var form_object = {"from1": from, "to1": to , "message1": message , "subject1": subject, "viewed1":viewed};
	if(to==''||message==''||subject==''){
		alert("Do you even message, bro?");
	} else {
		request_data('message_submit', form_object , function(json_data){
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

