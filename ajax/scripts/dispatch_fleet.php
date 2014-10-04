<?php
	
	// What if the user wants to send a subset of a current fleet as a new fleet?
	
	$this->require_login();
	
	load_class('Fleet');
	load_class('Traveling_Fleet');
	load_class('Fleet_Ship');
	load_class('Ship');
	
	// Sanitize inputs to this script.
	$script_inputs = array('fleet_id', 'to_x_coord', 'to_y_coord', 'primary_objective', 'secondary_objective');
	// TODO: uncomment this code when TJ makes the Ship class:
	//for ( $i = 0; $i < count(Ship::types); $i++ )
	//	$script_inputs[] = 'ship'. $i .'_count';
	foreach ( $script_inputs as $key => $val )
		$$key = clean_text($_GET[$key]);
	
	
	
	// Make sure this user owns the specified fleet.
	if ( $pool_fleet->owner != $User->id )
		return_warning('You do not own the specified fleet.');
	else
	{
		// Verified: this user owns the pool fleet.
	
		// Create a new fleet object to represent the fleet from which
		// the ships are to be sent.
		$pool_fleet = new Fleet($fleet_id);
		
		// See what ships are available to send.
		$ship_pool = $pool_fleet->get_ships();
		
		// Add ships to the array of ships to send.
		$ships_to_send = array();
		for ( $i = 0; $i < count(Ship::types); $i++ )
		{
			$ship_count = ${'ship'. $i .'_count'};
			if ( $ship_count > 0 )
			{
				$ships_to_send[] = new Fleet_Ship(
					array(
						'fleet_id' => $fleed_id,
						'type' => $i,
						'count' => $ship_count
					)
				);
			}
		}
		
		// If every ship in the pool fleet is selected, send the pool
		// fleet instead of creating a new fleet.
		if ( $ship_pool == $ships_to_send )
		{
			$departing_fleet = $pool_fleet;
		}
		else
		{
			// Create a new fleet in the database.
			$departing_fleet = new Fleet(
				array(
					'owner' => $pool_fleet->owner,
					'current_x_coord' => $pool_fleet->current_x_coord,
					'current_y_coord' => $pool_fleet->current_y_coord,
					'home_x_coord' => $pool_fleet->home_x_coord,
					'home_y_coord' => $pool_fleet->home_y_coord,
					'speed' => $pool_fleet->speed
				)
			);
			$departing_fleet->save_data();
		}
		
		// 
		$departing_fleet->primary_objective = $primary_objective;
		$departing_fleet->secondary_objective = $secondary_objective;
		
		// TODO: Calculate the travel_duration.
		$travel_duration = 300;
		
		$arrival_time = time() + $travel_duration;
		
		$travel_data = new Traveling_Fleet(
			array(
				'fleet_id' => $departing_fleet->id,
				'departure_time' => time(),
				'arrival_time' => $arrival_time,
				'to_x_coord' => $to_x_coord,
				'to_y_coord' => $to_y_coord,
				'from_x_coord' => $pool_fleet->current_x_coord,
				'from_y_coord' => $pool_fleet->current_y_coord
			)
		);
		$travel_data->save_data();
		
		// Save this new fleet movement data into the database.
		$departing_fleet->save_data();
		
	}
	
?>