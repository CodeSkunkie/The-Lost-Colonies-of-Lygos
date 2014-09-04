<?php

$this->require_login();
$this->layout = 'game';

$colony = new Colony($User->colony_ids[0]);
$colony->update_resources();
//print_r($colony);


?>

<div id="main_game_UI_div">
	
	<div id="game_secondary_screen">
		<div class="game_screen" id="map_screen">
			<br /><br />(Pretend you see a lot of hexagons here)
		</div>
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
	var current_screen = 'main';
	
	// This is an event listener that gets called when a link is clicked
	// that requires switching out the game screen.
	$('.screen_link').click(function() {
		// Grab the name of this screen
		var name = $(this).attr('id').substr(9);
		// Call the data-fetching script for this screen.
		request_data('game_screen_' + name, function(json_data) {
			// If data was successfully fetched...
			if ( json_data.ERROR == "" )
			{
				// Populate the content of this screen.
				build_screen_contents(name, json_data);
				// Display this screen.
				change_screen(name);
			}
		});
	});
	
	function build_screen_contents(name, json_data) {
		if ( name == "map" ) {
			
		}
		else if ( name == "colony_management" ) {
			var colony = json_data.colony;
			//var buildings = json_data.buildings;
		}
		else
			alert("error: There is no game-screen for that link!");
	}
	
//	$('#map_hologram_div').click(function() {
//		change_screen('map');
//	});
//	
//	$('#colony_man_link_div').click(function() {
//		change_screen('colony_management');
//	});
	
	function change_screen(name) {
		$('#game_secondary_screen').hide(300, function() {
			$('#'+ current_screen +'_screen').hide();
			$('#'+ name + '_screen').show();
			$('#game_secondary_screen').show(300);
			current_screen = name;
		});
	}
</script>