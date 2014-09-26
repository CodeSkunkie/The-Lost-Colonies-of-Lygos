<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Job.php');
	require(WEBROOT .'classes/Colony_Building.php');
	
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
			// Create a building object for this upgraded building.
			$building = new Colony_Building($job->building_type, $job->building_id);
			// Tell this building object to do whatever it is supposed
			// to do whenever it gets upgraded.
			$building->finish_upgrade();
			$building->save_data();
			
			// Remove job from job queue.
			$job->delete();
		}
	}
	
	//print_arr($this->data['jobs']);
?>