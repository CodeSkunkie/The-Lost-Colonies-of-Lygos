<?php

$this->require_login();
$this->layout = 'game';

require(WEBROOT .'classes/Map_Tile.php');

$colony = new Colony($User->colony_ids[0]);
$colony->update_resources();

//print_r($colony);

?>

<div id="main_game_UI_div">
	
	<div id="game_secondary_screen_backdrop"></div>
	<div id="game_secondary_screen">
		<div class="game_screen" id="map_screen">
			<div id="map_container_div"></div>
			<div id="map_tile_selector_div"></div>
			<div id="sector_info_bg_div">
				<div id="sector_info_bg_title_div"></div>
				<div id="sector_info_bg_content_div"></div>
			</div>
			<div id="sector_info_div">
				<div id="sector_info_title_div">Sector Info</div>
				<div id="sector_info_content_div">Hover over a sector for more info<br /><br />Click a sector to see possible actions.</div>
			</div>
			<div id="navigation_panel_div"></div>
			<map name="nav_panel_map" id="nav_panel_map">
				<area id="nav_negY" class="map_nav_element" shape="polygon" 
						coords="49,35,49,18,41,18,57,5,72,18,64,18,64,35">
				<area id="nav_negX" class="map_nav_element" shape="polygon" 
						coords="34,50,17,41,12,47,7,29,28,23,25,30,42,39">
				<area id="nav_negZ" class="map_nav_element" shape="polygon" 
						coords="35,56,18,64,13,59,8,77,29,83,25,76,44,67">
				<area id="nav_posY" class="map_nav_element" shape="polygon" 
						coords="49,70,49,88,40,87,57,101,73,87,64,87,64,70">
				<area id="nav_posX" class="map_nav_element" shape="polygon" 
						coords="79,56,71,67,89,76,84,82,106,77,101,59,96,65">
				<area id="nav_posZ" class="map_nav_element" shape="polygon" 
						coords="79,51,70,40,88,31,84,24,106,29,101,48,96,42">
				<area id="nav_home" class="map_nav_element" shape="polygon" 
						coords="48,41,40,53,48,64,64,64,73,53,64,41">
			</map>
			<div id="sector_menu">
				<ul>
					<li>
						Scout
					</li>
					<li>
						Attack
					</li>
					<li>
						Occupy
					</li>
				</ul>
			</div>
		</div>
		<div class="game_screen" id="colony_management_screen">
			<div id="buildings_container"></div>
			<div id="building_info_div1"></div>
			<div id="building_info_div2"></div>
			<img id="unselect_bldg_btn" onclick="javascript:unselect_building();" src="media/themes/default/images/x.png" />
			<div id="unbuilt_building_menu">
				<div id="unbuilt_building_menu_title">Select a module to construct</div>
				<img id="hide_unbuilt_buildings_menu_btn" onclick="javascript:hide_unbuilt_buildings_menu();" src="media/themes/default/images/x.png" />
				<div id="unbuilt_building_list"></div>
			</div>
		</div>
		<div class="game_screen" id="messaging_screen">
			<div id="messaging_menu">
				<div class="messaging_button" id="inbox_button" onclick="javascript:display_inbox()">INBOX</div>
				<div class="messaging_button" id="sent_button" onclick="javascript:display_sent()">SENT</div>
				<img src="media/themes/default/images/refresh.png" onclick="javascript:get_messages();" id="refresh_messages">
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
		<div class="menu_title_mini">RESOURCES</div>
		<div id="col_res_out_div">
			<?php $colony->resources->print_summary(); ?>
		</div>
	</div>
	
	<div id="job_queue_div_mini" style="position:absolute; top:310px; left:10px; height:260px; width:150px; text-align:left; color:#ffffff;" >
		<div class="menu_title_mini">JOB STATUS</div>
		<div id="jobs_list_mini">loading...</div>
	</div>
	
	<div id="messaging_div_mini" style="position:absolute; top:148px; left:836px; height:295px; width:150px; text-align:left; color:#ffffff;" >
		<div class="menu_title_mini">MESSAGING</div>
		<img src="media/themes/default/images/maximize.gif" onclick="javascript:maximize_messages();" class="maximize_screen">
		<table id="message_display_table_mini"></table>
		
	</div>
	
</div>






<script type="text/javascript">
	// Keep track of which game screen is currently being displayed.
	var current_screen;
	var player_id = <?php echo $User->id; ?>;
	var player_username = '<?php echo $User->username; ?>';
	
	// Keep track of which tile is at the center of the map screen.
	// Center the map on this player's colony to start with.
	var center_tile_x = <?php echo $colony->x_coord; ?>;
	var center_tile_y = <?php echo $colony->y_coord; ?>;

	// Keep track of player's home colony tile
	var home_tile_x = center_tile_x;
	var home_tile_y = center_tile_y;
	
	var colony_id = <?php echo $colony->id; ?>;
	var theme = 'default';
	// An array of buildings' data for this colony.
	var buildings = new Array();
	// An array for buildings' data that is derived.
	var buildings_method_data = new Array();
	
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
</script>
<script src="javascript/buildings.js"></script>
<script src="javascript/job_queue.js"></script>
<script src="javascript/map.js"></script>
<script src="javascript/messaging.js"></script>
<script src="javascript/resources.js"></script>
