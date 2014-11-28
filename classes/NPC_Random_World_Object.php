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
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript');

	protected function NPC_resources() {
	}
	
	// This method gets called when a fleet comes to collect resources from this object.
	public function yield_resources($fleet_capacity)
	{
		switch ($this->building_type) {
			case 0:
				$food = ceil($fleet_capacity * 0.1);
				$water = ceil($fleet_capacity * 0.1);
				$metal = ceil($fleet_capacity * 0.6);
				$energy = ceil($fleet_capacity * 0.2);
				break;
			case 2:
				$food = ceil($fleet_capacity * 0.2);
				$water = ceil($fleet_capacity * 0.4);
				$metal = ceil($fleet_capacity * 0.2);
				$energy = ceil($fleet_capacity * 0.2);
				break;
			case 1:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
				$food = ceil($fleet_capacity * 0.1);
				$water = ceil($fleet_capacity * 0.2);
				$metal = ceil($fleet_capacity * 0.3);
				$energy = ceil($fleet_capacity * 0.4);
				break;
			default:
				$food = ceil($fleet_capacity * 0);
				$water = ceil($fleet_capacity * 0);
				$metal = ceil($fleet_capacity * 0);
				$energy = ceil($fleet_capacity * 0);
		}
		
		return new Resource_Bundle($food, $water ,$metal, $energy);
	}
}


?>