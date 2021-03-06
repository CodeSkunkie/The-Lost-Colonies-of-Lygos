<?php

class Asteroid_World_Object extends World_Object {
	// Fields taken directly from the database:
	public $id, $type=0, $x_coord, $y_coord, $owner;
	protected $mass = -1; // -1 for inexhaustible, immobile resources. May be changed in child classes

	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field
	//		name to the $extra_fields array too.
	public $name = 'Asteroid ';//.mt_rand(0, 5);
	public $long_descript = "One of the many 'space rocks' drifting lazily through the void. Has pockets of water and lots of metal.";
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript');

	// This method gets called when a fleet comes to collect resources from this object.
	public function yield_resources($fleet_capacity)
	{
		$metal = ceil($fleet_capacity * 0.8);
		$water = ceil($fleet_capacity * 0.2);
		$food = 0;
		$energy = 0;
		return new Resource_Bundle($food, $water , $metal, $energy);
	}
}


?>