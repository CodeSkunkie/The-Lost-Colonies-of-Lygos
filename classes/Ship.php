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
	// Static constructor for constructing children buildings:
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
	public $attack, $defense;
	
	
	public function build_cost()
	{
		return new Resource_Bundle(5,5,5,5);
	}
	
	// Returns the upkeep cost for a given level of this building.
	protected function upkeep_cost($level)
	{
		return new Resource_Bundle(0,0,0,1);
	}
	
	public function build_duration()
	{
		return 200; // seconds
	}
	
	// This function gets called whenever a ship of this type is built.
	// $colony is a reference to this building's colony.
	public function finish_upgrade($colony)
	{
		$additional_upkeep = $this->upkeep_cost();
		foreach ( $additional_upkeep as $field => $val )
			$colony->resources->$field->consumption_rate += $val;
	}
}

?>