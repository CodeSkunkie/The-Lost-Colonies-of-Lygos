<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Colony.php');
	require(WEBROOT .'classes/Colony_Building.php');
	require(WEBROOT .'classes/Resource_Bundle.php');
	
	// Retrieve and sanatize input variables for this script.
	$colony_id = clean_text($_GET['colony_id']);
	$building_type = clean_text($_GET['building_type']);
	
	// Auth check: Make sure this user owns the selected colony.
	if ( !$User->owns_colony($colony_id) )
		$this->data['WARNING'] = 'This colony does not contain the spcecefied building.';
	else
	{
		// Verified: user owns this colony.
		$colony = new Colony($colony_id);
		
		// Create a generic building object of the specified type.
		$building = new Colony_Building($building_type);
		$building->level = 0;
		
		// Does the colony have enough resources to pay the construction price?
		if ( !$colony->can_afford($building->upgrade_cost()) )
		{
			return_warning('insufficient_resources');
		}
		else
		{
			// Colony can afford this construction project.
			
			// Make sure this building is not already being upgraded.
			$upgraded_check_qry = $Mysql->query("SELECT * FROM `job_queue`
				WHERE `building_type` = '". $building_type ."' AND 
					`colony_id` = '". $colony_id ."'");
			if ( $upgraded_check_qry->num_rows > 0 )
			{
				return_error('That module is already under construction.');
			}
			else
			{
				// Pay the upgrade cost.
				$colony->subtract_resources($building->upgrade_cost());
				
				// Insert this upgrade into the job queue.
				$Mysql->query("INSERT INTO `job_queue` SET
					`colony_id` = '". $colony_id ."',
					`building_id` = 0,
					`building_type` = '". $building_type ."',
					`start_time` = ". time() .",
					`completion_time` = '". (time() + $building->upgrade_duration()) ."'");	
				$colony->save_data();
			}
		}
	}
	
?>