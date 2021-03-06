<?php

class Wreckage_World_Object extends World_Object {
	 // Fields taken directly from the database:
	public $id, $type=3, $x_coord, $y_coord, $owner;
	protected $mass = -1; // -1 for inexhaustible, immobile resources. May be changed in child classes

	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field
	//		name to the $extra_fields array too.
	public $name = 'Space Junk';
	public $long_descript = "With so many battles occuring between different colonies, it is surprising you don't see more wreckage around. There might be some salvageable resources here.";
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript');
	
	// This method gets called when a fleet comes to collect resources from this object.
	public function yield_resources($fleet_capacity)
	{
		$metal = ceil($fleet_capacity * 0.6);
		$water = ceil($fleet_capacity * 0.1);
		$food = ceil($fleet_capacity * 0.1);;
		$energy = ceil($fleet_capacity * 0.2);;
		return new Resource_Bundle($food, $water , $metal, $energy);
	}
}


?>