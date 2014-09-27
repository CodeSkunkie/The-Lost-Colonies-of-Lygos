<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Colony.php');
	require(WEBROOT .'classes/Colony_Building.php');
	require(WEBROOT .'classes/Resource_Bundle.php');
	
	// Retrieve and sanatize input variables for this script.
	$colony_id = clean_text($_GET['colony_id']);
	$building_id = clean_text($_GET['building_id']);
	$building_type = clean_text($_GET['building_type']);
	
	// Auth check: Make sure this user owns the selected colony.
	if ( !$User->owns_colony($colony_id) )
		$this->data['WARNING'] = 'This colony does not contain the spcecefied building.';
	else
	{
		// Verified: user owns this colony.
		$colony = new Colony($colony_id);
		
		// See if this colony has the specified building.
		$bldg_qry = $Mysql->query("SELECT * FROM `buildings`
			WHERE `id` = '". $building_id ."' ");
		if ( $bldg_qry->num_rows == 0 ) //while ( $bldg_row = $bldg_qry->fetch_assoc() )
			$this->data['WARNING'] = 'Cannot upgrade a building that has not been built.';
		else
		{
			// Verified: the specified colony has the specified building.
			
			// Create a building object.
			$bldg_class_name = Colony_Building::type2classname($building_type);
			require(WEBROOT .'classes/'. $bldg_class_name .'.php');
			$building = new $bldg_class_name($building_id);
			
			
			
			// Does the colony have enough resources to pay the upgrade price?
			if ( !$colony->can_afford($building->upgrade_cost()) )
			{
				return_warning('insufficient_resources');
			}
			else
			{
				// Colony can afford this upgrade.
				
				// Make sure this building is not already being upgraded.
				$upgraded_check_qry = $Mysql->query("SELECT * FROM `job_queue`
					WHERE `building_id` = '". $building_id ."' ");
				if ( $upgraded_check_qry->num_rows > 0 )
				{
					return_error('The selected building is already being upgraded.');
				}
				else
				{
					// Pay the upgrade cost.
					$colony->subtract_resources($building->upgrade_cost());
					
					// Run the specified building's begin_upgrade function.
					$building->begin_upgrade();
					
					// Insert this upgrade into the job queue.
					$Mysql->query("INSERT INTO `job_queue` SET
						`colony_id` = '". $colony_id ."',
						`building_id` = '". $building_id ."',
						`building_type` = '". $building_type ."',
						`start_time` = ". time() .",
						`completion_time` = '". (time() + $building->upgrade_duration()) ."'");	
					$colony->save_data();
				}
			}
		}
	}
	
?>