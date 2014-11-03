<?php

class Planet_World_Object extends World_Object {
	// Fields taken directly from the database:
	public $id, $type=1, $x_coord, $y_coord, $owner;
	protected $mass = -1; // -1 for inexhaustible, immobile resources. May be changed in child classes

	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field
	//		name to the $extra_fields array too.
	public $name = 'Planet ' + substr(md5(mt_rand()), 0, 4);
	public $long_descript = "One of the more massive bodies out there. Perhaps someone lives here? Source of food, water, and metal.";
	protected $resource_bundle = 'planet_resources';
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript', 'resource_bundle');

	protected function planet_resources() {
		return new Resource_Bundle(10,40,30,0);
	}

	protected extract_mass() {
		// to be implemented later with depletable resources
		// each space resource performs this function differently
	}
}


?>