<?php

abstract class Ship 
{
	// Static class stuff:
	// DO NOT RE-ORDER THIS ARRAY. DO NOT DELETE ELEMENTS. APPEND TO END ONLY.
	public static $types = array('Fighter', 'Scout', 'Tank', 'Cargo');
	// Given a ship type number, return its class name.
	public static function type2classname($type)
	{
		return ( self::$types[$type] .'_Ship');
	}
	// Static constructor for constructing children ships:
	// The input parameter $fields is an associative array of $field-$value pairs.
	// $fields['type'] must ALWAYS be specified.
	// If input parameter $fetch_data is false, no data will be retrieved 
	// from the database for this object. Fetching also requires $fields['id'].
	public static function construct_child($fields, $fetch_data = false)
	{
		// This particular class has no database table so fetch_data should be false,
		// but the.
		$fetch_data = false;
		if ( !isset($fields['type']) || !isset(Ship::$types[$fields['type']]) )
			return NULL;
		
		$ship_class_name = Ship::type2classname($fields['type']);;
		load_class($ship_class_name);
		return new $ship_class_name($fields);
	}
	
	// TODO: define the various ship attribute fields.
	public $type, $attack, $defense, $hp, $shield, $capacity, $cargo, $speed, 
		$level, $evasion, $accuracy;
	
	public $name;
	public $long_descript;
	
	// Returns the upkeep cost for a given level of this ship.
	protected function upkeep_cost()
	{
		if ($this->$level == 0)
			return new Resource_Bundle(0,0,0,0);
		
		return new Resource_Bundle(
			0,
			0,
			0,
			1 + round($this->$level * 0.2));
	}
	
	public function build_cost()
	{
		return new Resource_Bundle(
			2 * $this->level + 5,
			2 * $this->level + 5,
			2 * $this->level + 5,
			2 * $this->level + 5);
	}
	
	public function build_duration()
	{
		return ($this->level + 1) * 30; // seconds
	}
	
	// This function gets called whenever a ship of this type is built.
	// $colony is a reference to this building's colony.
	public function finish_build($colony)
	{
		$additional_upkeep = $this->upkeep_cost();
		foreach ( $additional_upkeep as $field => $val )
			$colony->resources->$field->consumption_rate += $val;
	}
}

?>