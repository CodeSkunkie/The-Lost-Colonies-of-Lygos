<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Job.php');
	require(WEBROOT .'classes/Colony_Building.php');
	require(WEBROOT .'classes/Colony.php');
	require(WEBROOT .'classes/Resource_Bundle.php');
	
	// Retrieve and sanatize input variables for this script.
	$job_id = clean_text($_GET['job_id']);
	
	// Retrieve the job.
	$job = new Job($job_id);
	// Make sure this job exists.
	if ( !$job->exists() )
		return_warning("Specified job not found (already completed?)");
	else
	{
		// Job exists.
		
		// Make sure job completion-time has passed.
		if ( time() < $job->completion_time )
			return_warning("Specified job has not yet reached completion.");
		else
		{
			// Does this building exist already, or does this job create it?
			if ( $job->building_id == 0 )
			{
				// Create a building object for a brand-new building.
				$building = new Colony_Building($job->building_type);
				// Building's level will be incremented when finish_upgrade() is called.
				$building->level = 0;
				$building->colony_id = $job->colony_id;
				// Insert this new building into the DB.
				// This will also give the building object an id.
				$building->save_data();
			}
			else
			{
				// Create a building object for an existing building.
				$building = new Colony_Building($job->building_type, $job->building_id);
			}
			// Create a colony object to manipulate.
			$colony = new Colony($job->colony_id);
			// Tell this building object to do whatever it is supposed
			// to do whenever it gets upgraded.
			$building->finish_upgrade($colony);
			$building->save_data();
			$colony->save_data();
			
			// Remove job from job queue.
			$job->delete();
		}
	}
	
	//print_arr($this->data['jobs']);
?>