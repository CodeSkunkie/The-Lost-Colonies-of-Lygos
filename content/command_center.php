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
			<!-- HTML for the messaging screen goes here -->
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
		<div class="menu_title_mini">messaging</div>
		<div class="maximize_screen">
			<a href="javascript:get_messages();"><img src="media/images/maximize.gif"></a>
		</div>
		
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
	
	// This function is called when someone clicks the messaging link.
	function get_messages () {
		// Grab the name of this screen
		var name = 'messaging';
		// Call the data-fetching script for this screen.
		request_data('game_screen_' + name, function(json_data) {
			// If data was successfully fetched...
			if ( json_data.ERROR == "" )
			{
				//Iterate through the messages
				//Check for unread messages
				//Display messages
				
				//Separate query to check if there are new messages
				
				// Display this screen.
				change_screen(name);
			}
			else
				alert(json_data.ERROR);
		});
	}
	
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
