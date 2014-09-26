<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Colony_Building.php');
	
	// Retrieve and sanatize input variables for this script.
	$colony_id = clean_text($_GET['colony_id']);
	
	// Auth check: Make sure this user owns the selected colony.
	if ( !$User->owns_colony($colony_id) )
		$this->data['WARNING'] = 'You do not own the specified colony.';
	else
	{
		$this->data['jobs'] = array();
		// Retrieve this colony's jobs.
		$jobs_qry = $Mysql->query("SELECT * FROM `job_queue`
			WHERE `colony_id` = '". $colony_id ."' ");
		while ( $job_row = $jobs_qry->fetch_assoc() )
		{
			$job = $job_row;
			// Retrieve some additional info about this job.
			$building = new Colony_Building($job['building_type'], $job['building_id']);
			
			$job['old_level'] = $building->level;
			$job['new_level'] = $building->level +1;
			$job['building_name'] = $building->name;
			
			$this->data['jobs'][] = $job;
		}
	}
	
	//print_arr($this->data['jobs']);
?>