<?php

	$this->require_login();
	
	load_class('Colony_Building');
	load_class('Resource_Bundle');
	
	$colony_id = clean_text($_GET['colony_id']);
	
	// Auth check: Make sure this user owns the selected colony.
	if ( !$User->owns_colony($colony_id) )
		$this->data['WARNING'] = 'You do not own the specified colony.';
	else
	{
		$this->data['buildings'] = array();
		$this->data['buildings_method_data'] = array();
		$bldg_qry = $Mysql->query("SELECT * FROM `buildings`
		WHERE `colony_id` = '". $colony_id ."'");
		while ( $bldg_row = $bldg_qry->fetch_assoc() )
		{
			$bldg_class_name = Colony_Building::type2classname($bldg_row['type']);
			load_class($bldg_class_name);
			$building = new $bldg_class_name($bldg_row);
			$this->data['buildings'][] = $building;
			$this->data['buildings_method_data'][] = array(
				'cost' => $building->upgrade_cost(),
				'upkeep1' => $building->current_upkeep_cost(),
				'upkeep2' => $building->next_upkeep_cost(),
				'duration' => $building->upgrade_duration());
		}
	}
	
?>