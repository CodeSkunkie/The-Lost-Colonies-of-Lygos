<?php

/*
 * Given a station's ID, this function calculates and updates current resource 
 * levels for that station based on its rate of production and the time of the 
 * preceding update.
 * Returns an associative array of resource levels and production rates.
 */
function update_resources($station_id)
{
	global $db_link;
	
	// Make sure that the user accessing this page owns the station.
	$station_query = mysql_query("SELECT * FROM `stations`
		WHERE `id` = ". $station_id ." AND
			`user_id` = ". $_SESSION['user_id'] ." ", $db_link);
	if ( mysql_num_rows($station_query) == 0 )
		echo go_home();
	else
		$station = mysql_fetch_assoc($station_query);
	
	$resource_query = mysql_query("SELECT * FROM `resources`
		WHERE `station_id` = ". $station['id'] ." ", $db_link);
	$resources = mysql_fetch_assoc($resource_query);
	
	// Calculate current resource levels based on growth rates.
	$resources['food'] = $resources['food'] +
		(
			$resources['food_rate'] * 
			(time() - $resources['last_updated']) *
			0.000277777778
		);
	$resources['water'] = $resources['water'] +
		(
			$resources['water_rate'] * 
			(time() - $resources['last_updated']) *
			0.000277777778
		);
	$resources['energy'] = $resources['energy'] +
		(
			$resources['energy_rate'] * 
			(time() - $resources['last_updated']) *
			0.000277777778
		);
	$resources['metal'] = $resources['metal'] +
		(
			$resources['metal_rate'] * 
			(time() - $resources['last_updated']) *
			0.000277777778
		);
	
	// Save new resource levels to the database.
	mysql_query("UPDATE `resources`
		SET `food` = ". $resources['food'] .", 
			`water` = ". $resources['water'] .", 
			`metal` = ". $resources['metal'] .", 
			`energy` = ". $resources['energy'] .", 
			`last_updated` = ". time() ." 
		WHERE `station_id` = ". $station['id'] ." ", $db_link);
	
//	// Debugging output: updated resource levels.
//	echo  floor($resources['energy']) .', '. 
//		floor($resources['food']) .', '. 
//		floor($resources['metal']) .', '. 
//		floor($resources['water']) .', '. 
//		$resources['credits'];
		
	return $resources;
}

?>