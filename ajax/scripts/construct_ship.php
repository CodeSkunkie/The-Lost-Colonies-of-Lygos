<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Colony.php');
	require(WEBROOT .'classes/Ship.php');
	require(WEBROOT .'classes/Resource_Bundle.php');
	
	// Retrieve and sanatize input variables for this script.
	$colony_id = clean_text($_GET['colony_id']);
	$ship_type = clean_text($_GET['ship_type']);
	
	// Auth check: Make sure this user owns the selected colony.
	if ( !$User->owns_colony($colony_id) )
		$this->data['WARNING'] = 'This User does not own this colony.';
	else
	{
		// Verified: user owns this colony.
		$colony = new Colony($colony_id);
		
		// Create a generic ship object of the specified type.
		$ship = Ship::construct_child(['type' => $ship_type]);
		$ship->level = 0;
		
		// Does the colony have enough resources to pay the construction price?
		if ( !$colony->can_afford($ship->build_cost()) )
		{
			return_warning('insufficient_resources');
		}
		else
		{
			// Colony can afford this construction project.
						
			// Pay the build cost.
			$colony->subtract_resources($ship->build_cost());
			
			// Insert this upgrade into the job queue.
			$Mysql->query("INSERT INTO `job_queue` SET
				`colony_id` = '". $colony_id ."',
				`type` = 1,
				`product_id` = 0,
				`product_type` = '". $ship_type ."',
				`start_time` = ". time() .",
				`duration` = '". $ship->upgrade_duration() ."',
				`completion_time` = '". (time() + $ship->upgrade_duration()) ."'");	
			$colony->save_data();
			
		}
	}
	
?>