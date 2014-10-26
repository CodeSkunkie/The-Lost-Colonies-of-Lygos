<?php
	
	// This script returns a fleet object (with fleet ships attached) 
	// belonging to the current player at the specified coordinates.
	// given these inputs:
	//		x: the x coordinate of the fleet
	//		y: the y coordinate of the fleet
	
	$this->require_login();

	load_class('Fleet');
	load_class('Traveling_Fleet');
	load_class('Fleet_Ship');
	load_class('Ship');
	
	// Sanitize inputs to this script and create variables of the same name.
	$script_inputs = array('x', 'y', 'colony_id');
	foreach ( $script_inputs as $varname )
		$$varname = clean_text($_GET[$varname]);
	
	// Get the fleet (if any) and return it.
	$fleets = Fleet::fleets_at($x, $y, $User->id);
	if ( !empty($fleets) )
	{
		$fleet = $fleets[0];
		$fleet->get_ships();
	}
	else
		$fleet = false;
	$this->data['fleet'] = $fleet;
	
	// Return some ship objects to reference as well.
	$ref_ships = array();
	for ( $i = 0; $i < count(Ship::$types); $i++ )
		$ref_ships[$i] = Ship::construct_child(['type' => $i]);
	$this->data['ref_ships'] = $ref_ships;
	
?>