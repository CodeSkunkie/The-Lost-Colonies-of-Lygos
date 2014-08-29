<?php
	$station_query = mysql_query("SELECT * FROM `stations`
		WHERE `user_id` = ". $_SESSION['user_id'] ." ", $db_link);
	$station = mysql_fetch_assoc($station_query);
	$resources = update_resources($station['id']);
	
	echo 'resources: <span id="resources_stored">loading...</span>';

?>

<script type="text/javascript">
	var station_id = <?php echo($station['id']); ?>;
	var food = <?php echo($resources['food']); ?>;
	var water = <?php echo($resources['water']); ?>;
	var metal = <?php echo($resources['metal']); ?>;
	var energy = <?php echo($resources['energy']); ?>;
	var credits = <?php echo($resources['credits']); ?>;
	var food_rate = <?php echo($resources['food_rate']); ?>;
	var water_rate = <?php echo($resources['water_rate']); ?>;
	var metal_rate = <?php echo($resources['metal_rate']); ?>;
	var energy_rate = <?php echo($resources['energy_rate']); ?>;
	var seconds_after_page_load = 0;
	
	// Calculate and display estimated resource levels.
	function resources_calculate()
	{
		seconds_after_page_load += 1;
		food = food + (food_rate * 0.000277777778);
		water = water + (water_rate * 0.000277777778);
		metal = metal + (metal_rate * 0.000277777778);
		energy = energy + (energy_rate * 0.000277777778);
		
		$("#resources_stored").html(Math.floor(energy) +', '+ Math.floor(food) +', '+ 
			Math.floor(metal) +', '+ Math.floor(water) +', '+ Math.floor(credits));
	}
	
	$(document).ready(function(){
		resources_calculate();
		setInterval("resources_calculate()", 1000);
	});
</script>