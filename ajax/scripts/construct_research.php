<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Colony.php');
	require(WEBROOT .'classes/Research_Item.php');
	require(WEBROOT .'classes/Resource_Bundle.php');

	// Retrieve and sanatize input variables for this script.
	$colony_id = clean_text($_GET['colony_id']);
	$research_type = clean_text($_GET['research_type']);
	// Auth check: Make sure this user owns the selected colony.
	if ( !$User->owns_colony($colony_id) )
		$this->data['WARNING'] = 'This User does not own this colony.';
	else
	{
		// Verified: user owns this colony.
		$colony = new Colony($colony_id);
		$research = Research_Item::construct_child(['type' => $research_type]);
		$research_qry = $Mysql->query("SELECT level FROM `research` WHERE `type` = '". $research->type ."' AND 
				`player_id` = '". $colony_id ."'");
		if($research_qry->num_rows != 0){
			$research_qry->data_seek(0);
			$user_row = $research_qry->fetch_assoc();
			$research->level = $user_row['level'] + 1;
		}else{
			$research->level = 1;
		}

		if ( !$colony->can_afford($research->research_cost()) )
		{
			return_warning('insufficient_resources');
		}
		else
		{
			$research_qry = $Mysql->query("UPDATE `research` SET `level`='". $research->level ."' WHERE `type` = '". $research->type ."' AND 
				`player_id` = '". $colony_id ."'");
			$upgraded_check_qry = $Mysql->query("SELECT * FROM `job_queue`
				WHERE `product_type` = '". $research->type ."' AND 
					`colony_id` = '". $colony_id ."'");
			if ( $upgraded_check_qry->num_rows > 0 )
			{
				return_error('That module is already under construction.');
			}
			else
			{
				$colony->subtract_resources($research->research_cost());
				
				$Mysql->query("INSERT INTO `job_queue` SET
					`colony_id` = '". $colony_id ."',
					`type` = 2,
					`product_id` = 0,
					`product_type` = '". $research->type ."',
					`start_time` = ". time() .",
					`duration` = '". $research->research_duration() ."',
					`completion_time` = '". (time() + $research->research_duration()) ."'");	
				$colony->save_data();
			}
		}
	}
	
?>