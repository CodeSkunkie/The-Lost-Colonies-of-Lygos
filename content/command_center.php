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
	
	<div id="game_secondary_screen">
		<div class="game_screen" id="map_screen"><!-- HTML for the map screen goes here --></div>
		<div class="game_screen" id="colony_management_screen">
			<br /><br />There... There is nothing here!<br />YOR CALONY IS DEEAAADDDD
		</div>
	</div>
	
	
	<div class="screen_link" id="link_div_map" style="position:absolute; top:515px; left:700px; height:100px; width:100px; cursor:pointer; color:#ffffff;" >
		[map hologram]
	</div>
	
	<div class="screen_link" id="link_div_colony_management" style="position:absolute; top:515px; left:350px; height:100px; width:300px; cursor:pointer; color:#ffffff;" >
		[colony management]
	</div>
	
	<div id="resource_info_div" style="position:absolute; top:25px; left:10px; height:260px; width:150px; text-align:left; color:#ffffff;" >
		<?php $colony->resources->print_summary(); ?>
	</div>
	
	<div id="job_queue_div" style="position:absolute; top:310px; left:10px; height:260px; width:150px; text-align:left; color:#ffffff;" >
		[construction / upgrade jobs progress]
	</div>
	
	
</div>


<script type="text/javascript">
	// Keep track of which game screen is currently being displayed.
	var current_screen;
	
	// Keep track of which tile is at the center of the map screen.
	// Center the map on this player's colony to start with.
	var center_tile_x = <?php echo $colony_tile->coord_x; ?>;
	var center_tile_y = <?php echo $colony_tile->coord_y; ?>;
	
	// This function is called when someone clicks the colony management link.
	$('#link_div_colony_management').click(function() {
		// Grab the name of this screen
		var name = $(this).attr('id').substr(9);
		// Call the data-fetching script for this screen.
		request_data('game_screen_' + name, function(json_data) {
			// If data was successfully fetched...
			if ( json_data.ERROR == "" )
			{
				// Populate the content of this screen.
				var colony = json_data.colony;
				
				// Display this screen.
				change_screen(name);
			}
			else
				alert(json_data.ERROR);
		});
	});
	
	// This function is called when someone clicks the map hologram.
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
				for (var i=0; i<10; i++) 
				{
					jQuery('<img>', {
						"id": 'map_tile'+ i +'div',
						"class": 'map_tile_div',
						"src": 'media/images/banana.png'
					}).appendTo('#'+ name +'_screen');
					$('#map_tile'+ i).hover(function() {
						// display this tile's info in the hover-over info box.
						// (see UI mock-up)
					});
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
		$('#game_secondary_screen').hide(300, function() {
			$('.game_screen').hide();
			$('#'+ name + '_screen').show();
			$('#game_secondary_screen').show(300);
			current_screen = name;
		});
	}
	
// Different older version:
//	// This is an event listener that gets called when a link is clicked
//	// that requires switching out the game screen.
//	// DO NOT EDIT THIS FUNCTION
//	$('.screen_link').click(function() {
//		// Grab the name of this screen
//		var name = $(this).attr('id').substr(9);
//		// Call the data-fetching script for this screen.
//		request_data('game_screen_' + name, function(json_data) {
//			// If data was successfully fetched...
//			if ( json_data.ERROR == "" )
//			{
//				// Populate the content of this screen.
//				build_screen_contents(name, json_data);
//				// Display this screen.
//				change_screen(name);
//			}
//		});
//	});
//	// This is the function you will edit.
//	function build_screen_contents(name, json_data) {
//		if ( name == "map" ) {
//			// Brian's stuff goes here
//			
//		}
//		else if ( name == "colony_management" ) {
//			var colony = json_data.colony;
//			//var buildings = json_data.buildings;
//		}
//		else
//			alert("error: There is no game-screen for that link!");
//	}
</script>