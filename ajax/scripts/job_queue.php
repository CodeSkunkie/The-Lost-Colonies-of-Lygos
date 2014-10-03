<?php

	$this->require_login();
	
	load_class('Job');
	load_class('Colony_Building');
	// TODO: load the classes for ships and research items.
	
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
			
			// Retrieve some peripheral data for this job.
			$product = Job::make_product_object($job['type'], $job['product_id'], 
					$job['product_type'], $job['colony_id']);
				
			if ( property_exists( get_class($product), 'level') )
			{
				$job['old_level'] = $product->level;
				$job['new_level'] = $product->level +1;
			}
			$job['product_name'] = $product->name;
			
			$this->data['jobs'][] = $job;
		}
	}
	
	//print_arr($this->data['jobs']);
?>