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
	protected $resource_bundle = 'random_resources';
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript', 'resource_bundle');

	protected function random_resources() {
		return new Resource_Bundle(mt_rand(0,3), mt_rand(0,10), mt_rand(0,20), mt_rand(0,10));
	}

	protected function extract_mass() {
		// to be implemented later with depletable resources
		// each space resource performs this function differently
	}
}


?>