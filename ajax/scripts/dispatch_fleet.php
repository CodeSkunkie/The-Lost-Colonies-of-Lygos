<?php
	
	$this->require_login();

	load_class('Fleet');
	load_class('Traveling_Fleet');
	load_class('Fleet_Ship');
	load_class('Ship');
	
	// Sanitize inputs to this script.
	$script_inputs = array('fleet_id', 'to_x_coord', 'to_y_coord', 
		'primary_objective', 'secondary_objective');
	for ( $i = 0; $i < count(Ship::$types); $i++ )
		$script_inputs[] = 'ship'. $i .'_count';
	foreach ( $script_inputs as $varname )
		$$varname = clean_text($_GET[$varname]);
	

	// Create a new fleet object to represent the fleet from which
	// the ships are to be sent.
	$pool_fleet = new Fleet($fleet_id);
	$pool_fleet->get_ships();
	
	// Make sure this user owns the specified fleet.
	if ( $pool_fleet->owner != $User->id )
		return_warning('You do not own the specified fleet.');
	else
	{
		// Verified: this user owns the pool fleet.
		// Create some ship objects to reference for stats.
		$ref_ships = Ship::get_reference_ships();
		
		// Add ships to the array of ships to send.
		$ships_to_send = array();
		for ( $i = 0; $i < count(Ship::$types); $i++ )
		{
			// count of ships to send for this ship type.
			$ship_count = ${'ship'. $i .'_count'};
			if ( $ship_count > 0 )
			{
				// Remove the outgoing ships from the pool fleet.
				if ( $ship_count > $pool_fleet->ships[$i]->count )
					return_warning('You cannot send more ships than you own.');
				else
					$pool_fleet->ships[$i]->count -= $ship_count
					
				// Add the outgoing ships to an array.
				$ships_to_send[$i] = new Fleet_Ship(
					array(
						'fleet_id' => $fleed_id,
						'type' => $i,
						'count' => $ship_count
					)
				);
			}
		}
		
		// If no ships are selected, reprimand the user.
		if ( empty($ships_to_send) )
			return_warning('You cannot dispatch a fleet of zero ships');
		
		// If every ship in the pool fleet is selected, send the pool
		// fleet instead of creating a new fleet.
		if ( $pool_fleet->ships == $ships_to_send )
		{
			$departing_fleet = $pool_fleet;
			$departing_fleet->traveling = 1;
		}
		else
		{
			// Create a new fleet object.
			$departing_fleet = new Fleet([
				'owner' => $pool_fleet->owner,
				'current_x_coord' => $pool_fleet->current_x_coord,
				'current_y_coord' => $pool_fleet->current_y_coord,
				'home_x_coord' => $pool_fleet->home_x_coord,
				'home_y_coord' => $pool_fleet->home_y_coord,
				'speed' => $pool_fleet->speed,
				'primary_objective' => $primary_objective,
				'secondary_objective' => $secondary_objective,
				'speed' => $fleet_speed
			]);
			// Save this new fleet to the DB to set its ID.
			$departing_fleet->save_data();
			// Set this new fleet object's ships...
			$departing_fleet->ships = $ships_to_send;
			// ... and save them to DB.
			$departing_fleet->save_ships();
		}
		
		// Calculate travel distance.
		$travel_distance = hex_distance(
				$departing_fleet->current_x_coord, 
				$departing_fleet->current_y_coord,
				$to_x_coord, $to_y_coord);
		
		// Calculate fleet speed.
		$departing_fleet->calculate_speed();
		
		// Calculate travel duration and arrival time.
		$travel_duration = $departing_fleet->speed * $travel_distance * 60 * 45;
		// (^ Here, a move speed of 1 can traverse a tile in 45 minutes.)
		$arrival_time = time() + $travel_duration;
		
		// Update the departing fleet with travel information.
		$departing_fleet->traveling = 1;
		$departing_fleet->departure_time = time();
		$departing_fleet->arrival_time = $arrival_time;
		$departing_fleet->to_x_coord = $to_x_coord;
		$departing_fleet->to_y_coord = $to_y_coord;
		$departing_fleet->from_x_coord = $pool_fleet->current_x_coord;
		$departing_fleet->from_y_coord = $pool_fleet->current_y_coord;
		
		// Save the departing fleet to the database.
		$departing_fleet->save_data();
		// Save the fships for this fleet into the DB.
		$departing_fleet->save_ships();
		
		// Save updated ship counts for the pool fleet into DB.
		$pool_fleet->save_ships();
		
		// Add this mission to the job queue.
		$Mysql->query("INSERT INTO `job_queue` SET
			`colony_id` = '". $colony_id ."',
			`type` = 3,
			`product_id` = '". $departing_fleet->id ."',
			`product_type` = '". $departing_fleet->primary_objective ."',
			`start_time` = ". time() .",
			`duration` = '". $travel_duration ."',
			`completion_time` = '". $departing_fleet->arrival_time ."'");	
	}
	
?>