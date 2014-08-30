<?php

$this->require_login();
$this->layout = 'game';

?>

<div id="main_game_UI_div">
	
	<div id="game_secondary_screen">
		<div class="game_screen" id="map_screen">
			<br /><br />(Pretend you see a lot of hexagons here)
		</div>
		<div class="game_screen" id="colony_management_screen">
			<br /><br />There... There's nothing here!<br />YOR CALONY IS DEEAAADDDD
		</div>
	</div>
	
	
	<div id="map_hologram_div" style="position:absolute; top:515px; left:700px; height:100px; width:100px; cursor:pointer; color:#ffffff;" >
		[map hologram]
	</div>
	
	<div id="colony_man_link_div" style="position:absolute; top:515px; left:350px; height:100px; width:300px; cursor:pointer; color:#ffffff;" >
		[colony management]
	</div>
	
	<div id="resource_info_div" style="position:absolute; top:25px; left:10px; height:260px; width:150px; text-align:left; color:#ffffff;" >
		[resource info]
	</div>
	
	<div id="job_queue_div" style="position:absolute; top:310px; left:10px; height:260px; width:150px; text-align:left; color:#ffffff;" >
		[construction / upgrade jobs progress]
	</div>
	
	
</div>


<script type="text/javascript">
	var current_screen = 'main';
	
	$('#map_hologram_div').click(function() {
		change_screen('map');
	});
	
	$('#colony_man_link_div').click(function() {
		change_screen('colony_management');
	});
	
	function change_screen(name) {
		request_data('game_screen_' + name, function(json) {
			//console.log(json);
			if ( json.ERROR == "" )
			{
				$('#game_secondary_screen').hide(300, function() {
					$('#'+ current_screen +'_screen').hide();
					$('#'+ name + '_screen').show();
					$('#game_secondary_screen').show(300);
					current_screen = name;
				});
			}
		});
	}
</script>