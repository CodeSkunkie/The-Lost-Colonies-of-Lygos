<?php

	$this->require_login();
	
	require(WEBROOT .'classes/Colony_Building.php');
	require(WEBROOT .'classes/Resource_Bundle.php');
	
	$basic_buildings = array(0,1,2,3,4,5);
	$already_built = array();
	
	$colony_id = clean_text($_GET['colony_id']);
	
	// Auth check: Make sure this user owns the selected colony.
	if ( !$User->owns_colony($colony_id) )
		$this->data['WARNING'] = 'You do not own the specified colony.';
	else
	{
		// See which buildings are already built here.
		$bldg_qry = $Mysql->query("SELECT * FROM `buildings`
		WHERE `colony_id` = '". $colony_id ."'");
		while ( $bldg_row = $bldg_qry->fetch_assoc() )
		{
			$already_built[] = $bldg_row['type'];
		}
		
		// TODO: See if any buildings are currently being built.
		
		// Only return info for buildings that are not built.
		$this->data['buildings'] = array();
		$this->data['cost'] = array();
		$this->data['upkeep'] = array();
		$this->data['duration'] = array();
		foreach ( $basic_buildings as $building_type )
		{
			if ( !in_array($building_type, $already_built) )
			{
				$building = Colony_Building::construct_child(['type' => $building_type]);
				$building->level = 0;
				$this->data['buildings'][] = $building;
				$this->data['cost'][] = $building->upgrade_cost();
				$this->data['upkeep'][] = $building->next_upkeep_cost();
				$this->data['duration'][] = $building->upgrade_duration();
			}
		}
	}
	
?>