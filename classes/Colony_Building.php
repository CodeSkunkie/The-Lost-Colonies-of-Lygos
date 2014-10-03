<?php

// This class is different from others in significant ways under the hood,
// but should behave as expected when treated the same as other classes.
// Basically, each instance of this class mimics an instance of one of
// its children by calling the child's functions. 
// The chiled is stored in $building_object.

abstract class Colony_Building extends Database_Row
{
	// Static class stuff:
	// DO NOT RE-ORDER THIS ARRAY. DO NOT DELETE ELEMENTS. APPEND TO END ONLY.
	public static $types = array('HQ', 'Water', 'Food', 'Metal', 'Energy', 'Research', 'Storage', 'Shipyard');
	// Given a building type number, return its class name.
	public static function type2classname($type)
	{
		return ( self::$types[$type] .'_Building');
	}
	// Static constructor for constructing children buildings:
	// The input parameter $fields is an associative array of $field-$value pairs.
	// $fields['type'] must ALWAYS be specified.
	// If input parameter $fetch_data is false, no data will be retrieved 
	// from the database for this object. Fetching also requires $fields['id'].
	public static function construct_child($fields, $fetch_data = true)
	{
		if ( !isset($fields['type']) || !isset(Colony_Building::$types[$fields['type']]) )
			return NULL;
		
		$bldg_class_name = Colony_Building::$types[$fields['type']] .'_building';
		load_class($bldg_class_name);
		return new $bldg_class_name($fields, $fetch_data);
	}
	
	// Fields taken directly from the database:
	public $id, $colony_id, $type, $level;
	
	// Extra fields:
	public $name;
	public $long_descript;
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript');
	
	function __construct($fields, $fetch_data = true)
	{
		foreach ( $fields as $field => $value )
			$this->$field = $value;
		
		// Grab all field data from the database if specified.
		if ( $this->exists() && $fetch_data )
			$this->fetch_data();
	}
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(
			10 * $this->level + 20,
			10 * $this->level + 20,
			15 * $this->level + 20,
			10 * $this->level + 20);
	}
	
	// Returns the upkeep cost of this building at its current level.
	public function current_upkeep_cost()
	{
		return $this->upkeep_cost($this->level);
	}
	
	// Returns the upkeep cost of this building at its next level.
	public function next_upkeep_cost()
	{
		return $this->upkeep_cost($this->level +1);
	}
	
	// Returns the upkeep cost for a given level of this building.
	protected function upkeep_cost($level)
	{
		if ($level == 0)
			return Resource_Bundle(0,0,0,0);
		
		return new Resource_Bundle(
			0,
			0,
			0,
			1 + round($level * 0.34));
	}
	
	public function upgrade_duration()
	{
		return ($this->level + 1) * 70;
	}
	
	// This function gets called whenever this building gets upgraded.
	public function begin_upgrade()
	{
		
	}
	
	// This function gets called whenever this building gets upgraded.
	// $colony is a reference to this building's colony.
	public function finish_upgrade($colony)
	{
		$additional_upkeep = $this->next_upkeep_cost();
		foreach ( $additional_upkeep as $field => $val )
			$colony->resources->$field->consumption_rate += $val;
		
		$this->level++;
	}
}

?>