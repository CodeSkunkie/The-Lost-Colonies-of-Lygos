<?php

	$this->require_login();
	
	load_class('Job');
	load_class('Colony_Building');
	load_class('Colony');
	load_class('Resource_Bundle');
	
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
			// Create an object of the product that this job was working on.
			$product = Job::make_product_object($job->type, $job->product_id, $job->product_type, $job->colony_id);
			
			// Create a colony object to manipulate.
			$colony = new Colony($job->colony_id);
			
			// Tell this product object to do whatever it is supposed
			// to do upon upgrade.
			if ( method_exists($product, 'finish_upgrade') )
				$product->finish_upgrade($colony);
			$product->save_data();
			$colony->save_data();
			
			// Remove job from job queue.
			$job->delete();
		}
	}
	
?>