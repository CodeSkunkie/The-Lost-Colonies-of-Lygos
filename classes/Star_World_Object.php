<?php

class Star_World_Object extends World_Object {
	// Fields taken directly from the database:
	public $id, $type=2, $x_coord, $y_coord, $owner;
	protected $mass = -1; // -1 for inexhaustible, immobile resources. May be changed in child classes

	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field
	//		name to the $extra_fields array too.
	public $name = 'Star ';//.mt_rand(0, 3);
	public $long_descript = "The most massive of celestial bodies. Full of energy.. and so tasty too!";
	protected $resource_bundle = 'star_resources';
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript', 'resource_bundle');

	protected function star_resources() {
		return new Resource_Bundle(0,0,0,50);
	}

	protected function extract_mass() {
		// to be implemented later with depletable resources
		// each space resource performs this function differently
	}
}


?>