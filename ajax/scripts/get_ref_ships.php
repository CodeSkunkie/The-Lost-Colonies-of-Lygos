<?php
	
	// This script returns an array of reference ships 
	
	require(WEBROOT .'classes/Ship.php');
	require(WEBROOT .'classes/Resource_Bundle.php');
	
	$this->require_login();

	load_class('Ship');
	
	$colony_id = clean_text($_GET['colony_id']);
	$basic_ships = array(0,1,2,3);
	
	// Auth check: Make sure this user owns the selected colony.
	if ( !$User->owns_colony($colony_id) )
		$this->data['WARNING'] = 'You do not own the specified colony.';
	else
	{
		//Get some reference ships
		$ref_ships = Ship::get_reference_ships();
		
		// Return some ship objects and their stuff
		$this->data['ships'] = array();
		// $this->data['name'] = array();
		// $this->data['descript'] = array();
		// $this->data['attack'] = array();
		// $this->data['defense'] = array();
		// $this->data['hp'] = array();
		// $this->data['shield'] = array();
		// $this->data['capacity'] = array();
		// $this->data['speed'] = array();
		// $this->data['accuracy'] = array();
		// $this->data['evasion'] = array();
		$this->data['cost'] = array();
		$this->data['upkeep'] = array();
		$this->data['duration'] = array();
		foreach ($basic_ships as $ship_type){
		
			$ship = $ref_ships[$ship_type];
			$this->data['ships'][] = $ship;
			// $this->data['name'][] = $ship->$name;
			// $this->data['descript'][] = $ship->$long_descript;
			// $this->data['attack'][] = $ship->$attack;
			// $this->data['defense'][] = $ship->$defense;
			// $this->data['hp'][] = $ship->$hp;
			// $this->data['shield'][] = $ship->$shield;
			// $this->data['capacity'][] = $ship->$capacity;
			// $this->data['speed'][] = $ship->$speed;
			// $this->data['accuracy'][] = $ship->$accuracy;
			// $this->data['evasion'][] = $ship->$evasion;
			$this->data['cost'][] = $ship->build_cost();
			$this->data['upkeep'][] = $ship->upkeep_cost();
			$this->data['duration'][] = $ship->upgrade_duration();
		}
	}
	
?>