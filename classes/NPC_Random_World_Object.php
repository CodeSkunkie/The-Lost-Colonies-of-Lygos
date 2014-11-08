<?php

class NPC_Random_World_Object extends World_Object {
	// Non-Player Colony world object.
	// Can be any of the Colony_Building objects with no player attached (abandoned?)
	// **** NEEDS A BIT MORE WORK. MAY NEED TO STORE BUILDING TYPE IN DATABASE ****
	// Fields taken directly from the database:
	public $id, $type=4, $x_coord, $y_coord, $owner;
	protected $mass = -1; // -1 for inexhaustible, immobile resources. May be changed in child classes

	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field
	//		name to the $extra_fields array too.
	public $name = 'Abandoned Colony Building';
	public $long_descript = "A lost and forgotten building from another colony. Salvagable resources depend on its original purpose.";
	private $building_type = mt_rand(0,7);
	protected $resource_bundle = 'NPC_resources';
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript', 'resource_bundle');

	protected function NPC_resources() {
		switch ($this->building_type) {
			case 0:
				return new Resource_Bundle(3,3,15,9);
				break;
			case 2:
				return new Resource_Bundle(9,21,12,12);
				break;
			case 1:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
				return new Resource_Bundle(6,12,18,24);
				break;
			default:
				return new Resource_Bundle(0,0,0,0);
		}
	}

	protected function extract_mass() {
		// to be implemented later with depletable resources
		// each space resource performs this function differently
	}

}


?>