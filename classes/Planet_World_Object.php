<?php

class Planet_World_Object extends World_Object {
	// Fields taken directly from the database:
	public $id, $type=1, $x_coord, $y_coord, $owner;
	protected $mass = -1; // -1 for inexhaustible, immobile resources. May be changed in child classes

	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field
	//		name to the $extra_fields array too.
	public $name = 'Planet ';//.mt_rand(0, 4);
	public $long_descript = "One of the more massive bodies out there. Perhaps someone lives here? Source of food, water, and metal.";
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript');
	
	// This method gets called when a fleet comes to collect resources from this object.
	public function yield_resources($fleet_capacity)
	{
		$food = ceil($fleet_capacity * 0.125);
		$water = ceil($fleet_capacity * 0.5);
		$metal = ceil($fleet_capacity * 0.375);
		$energy = ceil($fleet_capacity * 0);
		return new Resource_Bundle($food, $water ,$metal, $energy);
	}
}


?>