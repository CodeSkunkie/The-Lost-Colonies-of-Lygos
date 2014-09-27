<?php

// This class is different from others in significant ways under the hood,
// but should behave as expected when treated the same as other classes.
// Basically, each instance of this class mimics an instance of one of
// its children by calling the child's functions. 
// The chiled is stored in $building_object.

class Colony_Building extends Database_Row
{
	// Static class stuff:
	// DO NOT RE-ORDER THIS ARRAY. DO NOT DELETE ELEMENTS. APPEND TO END ONLY.
	public static $types = array('HQ', 'Water', 'Food', 'Metal', 'Energy', 'Research', 'Storage', 'Shipyard');
	// Given a building type number, return its class name.
	public static function type2classname($type)
	{
		return ( self::$types[$type] .'_Building');
	}
	
	// Fields taken directly from the database:
	public $id, $colony_id, $type, $level;
	
	// Extra fields:
	public $name;
	private $building_object;
	public $long_descript;
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'building_object', 'long_descript');
	
	// If no building_id is specified, the constructed object will
	// not reflect a specific instance of that building type. 
	public function __construct($building_type, $building_id = false)
	{
		$bldg_class_name = Colony_Building::$types[$building_type] .'_building';
		load_class($bldg_class_name);
		$this->building_object = new $bldg_class_name($building_id);
		foreach ( $this->building_object as $field => $value )
		{
			if ( $field != 'building_object' )
				$this->$field = $value;
		}
	}
	
	public function upgrade_cost()
	{
		// Deferr execution of this function to the specific building's object.
		if ( method_exists($this->building_object, 'upgrade_cost') )
			return $this->building_object->upgrade_cost();
		else
		{
			return new Resource_Bundle(
				10 * $this->level + 20,
				10 * $this->level + 20,
				15 * $this->level + 20,
				10 * $this->level + 20);
		}
	}
	
	// Returns the upkeep cost of this building at its current level.
	public function current_upkeep_cost()
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->current_upkeep_cost) )
			return $this->building_object->current_upkeep_cost();
		else
		{
			return $this->upkeep_cost($this->level);
		}
	}
	
	// Returns the upkeep cost of this building at its next level.
	public function next_upkeep_cost()
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->next_upkeep_cost) )
			return $this->building_object->next_upkeep_cost();
		else
		{
			return $this->upkeep_cost($this->level +1);
		}
	}
	
	// Returns the upkeep cost for a given level of this building.
	protected function upkeep_cost($level)
	{
		if ($level == 0)
			return Resource_Bundle(0,0,0,0);
		
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->upkeep_cost) )
			return $this->building_object->upkeep_cost($level);
		else
		{
			return new Resource_Bundle(
				0,
				0,
				0,
				1 + round($level * 0.34));
		}
	}
	
	public function upgrade_duration()
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->upgrade_duration) )
			return $this->building_object->upgrade_duration();
		else
			return ($this->level + 1) * 70;
	}
	
	// This function gets called whenever this building gets upgraded.
	public function begin_upgrade()
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->begin_upgrade) )
			return $this->building_object->begin_upgrade();
	}
	
	// This function gets called whenever this building gets upgraded.
	// $colony is a reference to this building's colony.
	public function finish_upgrade($colony)
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->finish_upgrade) )
			return $this->building_object->finish_upgrade();
		else
		{
			$additional_upkeep = $this->next_upkeep_cost();
			foreach ( $additional_upkeep as $field => $val )
				$colony->resources->$field->consumption_rate += $val;
			
			$this->level++;
		}
	}
}

?>