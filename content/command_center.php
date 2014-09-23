<?php

$this->require_login();
$this->layout = 'game';

require(WEBROOT .'classes/Map_Tile.php');

$colony = new Colony($User->colony_ids[0]);
$colony->update_resources();

$colony_tile = new Map_Tile($colony->tile_id);
//print_r($colony);


?>

<div id="main_game_UI_div">
	
	<div id="game_secondary_screen_backdrop"></div>
	<div id="game_secondary_screen">
		<div class="game_screen" id="map_screen">
			<!-- HTML for brian the map screen goes here -->
			<div id="sector_info_div"></div>
			<div id="navigation_panel_div"></div>
			<div id="map_container_div"></div>
		</div>
		<div class="game_screen" id="colony_management_screen">
			<div id="buildings_container"></div>
			<div id="building_info_div1"></div>
			<div id="building_info_div2"></div>
			<div id="unselect_bldg_btn" onclick="javascript:unselect_building();">[X]</div>
		</div>
		<div class="game_screen" id="messaging_screen">
			<div id="messaging_menu">
				<div class="messaging_button" id="inbox_button" onclick="javascript:display_inbox()">INBOX</div>
				<div class="messaging_button" id="sent_button" onclick="javascript:display_sent()">SENT</div>
				<img src="media/images/refresh.png" onclick="javascript:get_messages();" id="refresh_messages">
			</div>
			<div id="message_display_container"></div>
			<div id="message_viewer"></div>
			<div id="message_composer"></div>
		</div>
	</div>
	
	
	<div class="screen_link" id="link_div_map" style="position:absolute; top:515px; left:700px; height:100px; width:100px; cursor:pointer; color:#ffffff;" >
		[map hologram]
	</div>
	
	<div class="screen_link" id="link_div_colony_management" style="position:absolute; top:515px; left:350px; height:100px; width:300px; cursor:pointer; color:#ffffff;" >
		[colony management]
	</div>
	
	<div id="resource_info_div" style="position:absolute; top:25px; left:10px; height:260px; width:150px; text-align:left; color:#ffffff;" >
		<div>RESOURCES</div>
		<?php $colony->resources->print_summary(); ?>
	</div>
	
	<div id="job_queue_div" style="position:absolute; top:310px; left:10px; height:260px; width:150px; text-align:left; color:#ffffff;" >
		[construction / upgrade jobs progress]
	</div>
	
	<div id="messaging_div_mini" style="position:absolute; top:148px; left:836px; height:295px; width:150px; text-align:left; color:#ffffff;" >
		<div class="menu_title_mini">MESSAGING</div>
		<img src="media/images/maximize.gif" onclick="javascript:maximize_messages();" class="maximize_screen">
		<table id="message_display_table_mini"></table>
		
	</div>
	
</div>






<script type="text/javascript">
	// Keep track of which game screen is currently being displayed.
	var current_screen;
	var player_id = <?php echo $User->id; ?>;
	
	// Keep track of which tile is at the center of the map screen.
	// Center the map on this player's colony to start with.
	var center_tile_x = <?php echo $colony_tile->coord_x; ?>;
	var center_tile_y = <?php echo $colony_tile->coord_y; ?>;
	
	var colony_id = <?php echo $colony->id; ?>;
	var theme = 'default';
	var buildings = new Array();

	//Inbox or Sent? variable
	var inbox=true;
	//Populate messages
	get_messages();
	//Generate message composer
	generate_message_composer();
	//Check for new messages every 60 seconds
	setInterval(function(){get_messages()}, 60000);
	
	// This function is called when someone clicks the colony management link.
	$('#link_div_colony_management').click(function() {
		// Grab the name of this screen
		var name = $(this).attr('id').substr(9);
		// Call the data-fetching script for this screen.
		request_data('game_screen_' + name, {"colony_id": colony_id}, function(json_data) {
			// If data was successfully fetched...
			if ( json_data.ERROR == "" )
			{
				// Erase any old contents on this screen.
				$('#buildings_container').html('');
				
				// Populate the content of this screen.
				for ( var i in json_data.buildings )
				{
					var building = json_data.buildings[i];
					building.update_cost = json_data.update_cost;
					buildings[building.type] = building;
					$('<img>', {
						"id": 'building_'+ building.type +'img',
						"class": 'building_img',
						"src": 'media/themes/'+ theme +'/images/building'+ building.type +'.png',
						"onclick": 'javascript:select_building('+ building.type +')'
					}).appendTo('#buildings_container');
				}
				
				// Display this screen.
				change_screen(name);
			}
			else if ( json_data.ERROR == 'login_required' )
				display_login_form();
			else
				alert(json_data.ERROR);
		});
	});
	
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
							"onclick":'javascript:go_to_message('+message.id+');'
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
							"onclick":'javascript:go_to_message('+message.id+');'
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
				console.log(json_data);
		
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
		var time = <?php echo time() ?>;
		// Returns successful data submission message when the entered information is stored in database.
		var form_object = {"from1": from, "to1": to , "message1": message , "subject1": subject, "viewed1":viewed,"time1":time};
		if(to==''||message==''||subject==''){
			alert("Do you even message, bro?");
		} else {
			request_data('message_submit', form_object , function(json_data){
				generate_message_composer();
			});
		}
	});
	
	//Set messages as read when you click on them
	$('#message_unviewed').click(function(){
		var message_id = $(this).attr('id').substring(7);
		request_data('message_read', {"viewed":1,"id":message_id},function(json_data){	
			get_messages();
		})
	});
	
	// This function is called when someone clicks the map hologram. brian
	$('#link_div_map').click(function() {
		// Grab the name of this screen
		var name = $(this).attr('id').substr(9);
		// Call the data-fetching script for this screen.
		request_data('game_screen_' + name, {"coord_x": center_tile_x, "coord_y": center_tile_y}, function(json_data) {
			// If data was successfully fetched...
			if ( json_data.ERROR == "" )
			{
				// Brian edits around here, mostly.
				
				// Erase any old contents on this screen.
				$('#'+ name +'_screen').html('');
				
				// Populate the content of this screen.
				var tiles_data = json_data.tiles_data;
				// TODO: figure out how to _actually_ iterate over the axial coordinate system.
				//		 go here and search 'axial coordinates':
				//		 http://www.redblobgames.com/grids/hexagons/
				// center_tile_x, center_tile_y are x,y of player home base tile (in database?)
				
				// x,y offsets for start of map area
				var div_y_offset=100;
				var div_x_offset=177;
				var rel_x=0;
				var rel_y=-2;

				// 19 map tiles are shown at once, center tile is #10
				// 1st tile is (center-0,center-2)
				for (var i=0; i<19; i++) 
				{
					if (i==3) {rel_x+=4; rel_y++;}
					else if (i==7) {rel_x+=5; rel_y++;}
					else if (i==12) {rel_x+=5; rel_y++;}
					else if (i==16) {rel_x+=4; rel_y++;}

					var div=jQuery('<img>', {
						"id": 'map_tile'+ i + 'div',
						"class": 'map_tile_div',
						"src": 'media/images/banana.png',
						"title": '(' + (center_tile_x+i-rel_x) + ',' + (center_tile_y+rel_y) + ')',
						"x": (center_tile_x+i-rel_x),
						"y": (center_tile_y+rel_y)
					});
					if (i<3) div.offset({top:div_y_offset,left:div_x_offset});
					else if (i<7) div.offset({top:div_y_offset+60,left:div_x_offset-245});
					else if (i<8) div.offset({top:div_y_offset+121,left:div_x_offset-560});
					else if (i<12) div.offset({top:div_y_offset+40,left:div_x_offset});
					else if (i<16) div.offset({top:div_y_offset+101,left:div_x_offset-316});
					else div.offset({top:div_y_offset+81,left:div_x_offset-177});
					div.appendTo('#'+ name +'_screen');
					/*$(document).ready(function() {
						div.bind('mouseover mouseout click', function(event) {
    					var $tgt = $(event.target);
    					if (!$tgt.closest('.syntax_hilite').length) {
      						$tgt.toggleClass(event.type == 'click' ? 'outline-element-clicked' : 'outline-element');
      						alert(div)
    						}
    					//alert($tgt);
  						});
					});*/
					
					/*$('#map_tile'+ i).hover(function() {
						$(this).toggleClass('highLight')
						//$("p").css("background-color","yellow");
						// display this tile's info in the hover-over info box.
						// (see UI mock-up)
					});*/
				}
				// Display this screen.
				change_screen(name);
			}   
			else
				alert(json_data.ERROR);
		});
	});
	
	// This function manages the visual hiding and showing of the screens.
	// DO NOT EDIT THIS FUNCTION
	function change_screen(name) {
		
		$('#game_secondary_screen_backdrop').hide(300);
		$('#game_secondary_screen').hide(300, function() {
			$('.game_screen').hide();
			$('#'+ name + '_screen').show();
			$('#game_secondary_screen').show(300);
			$('#game_secondary_screen_backdrop').show(300);
			current_screen = name;
		});
	}
	
	// Interpose the login form over the rest of the game screens.
	function display_login_form()
	{
		
	}
	
	var bldg_original_x;
	var bldg_original_y;
	var bldg_original_z_index;
	var selected_building_type;
	function select_building(type)
	{
		// Get rid of any prior building selections.
		unselect_building();
		
		// keep track of which building we're selecting so we can
		// undo the actions on it we're about to perform.
		selected_building_type = type;
		var building = buildings[type];
		
		// Move the building's image temporarily.
		bldg_original_x = $('#building_'+ type +'img').css('left');
		bldg_original_y = $('#building_'+ type +'img').css('top');
		bldg_original_z_index =  $('#building_'+ type +'img').css('z-index');
		var new_x = ($('#building_info_div1').width() / 2) - ($('#building_'+ type +'img').width() / 2);
		var new_y = 30;
		//var new_y = ($('#building_info_div1').height() / 2) - ($('#building_'+ type +'img').height() / 2);

		$('#building_'+ type +'img').animate({
			"left": new_x +"px",
			"top": new_y +"px",
			"z-index": 200
		});
		
		// Populate the info div
		$('#building_info_div2').html('');
		$('<div/>', {
			"id": 'building_title_div',
			"text": building.name
		}).appendTo('#building_info_div2');
		$('<div/>', {
			"id": "building_info_text_div"
		}).appendTo('#building_info_div2');
		
		$('<div/>', {
			"text": "level: "+ building.level
		}).appendTo('#building_info_text_div');
		$('<div/>', {
			"text": "maintenance: "
		}).appendTo('#building_info_text_div');
		$('<div/>', {
			"text": "upgrade cost: "+ building.update_cost.food
		}).appendTo('#building_info_text_div');
		
		
		// animate the opening of div1.
		var building_info_div1_height = $('#building_info_div1').height();
		$('#building_info_div1').css("height", "1px");
		$('#building_info_div1').show();
		$('#building_info_div1').animate({
			"height": building_info_div1_height +"px"
		}, function() {
			$('#unselect_bldg_btn').show();
			
			// animate the opening of div2.
			var building_info_div2_width = $('#building_info_div2').width();
			$('#building_info_div2').css("width", "1px");
			$('#building_info_div2').show();
			$('#building_info_div2').animate({
				"width": building_info_div2_width +"px"
			});
		});
		
		$('#building_'+ selected_building_type +'img').attr("onclick", "unselect_building()");
	}
	
	function unselect_building()
	{
		$('#building_'+ selected_building_type +'img').animate({
			"left": bldg_original_x,
			"top": bldg_original_y,
			"z-index": bldg_original_z_index
		});
		$('#building_'+ selected_building_type +'img').attr("onclick", "select_building("+ selected_building_type +")");
		
		$('#building_info_div1').hide();
		$('#building_info_div2').hide();
		
		$('#unselect_bldg_btn').hide();
		
		selected_building_type = false;
	}
	
</script>
