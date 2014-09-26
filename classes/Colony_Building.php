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
	public $building_object;
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'building_object');
	
	// If no building_id is specified, the constructed object will
	// not reflect a specific instance of that building type. 
	public function __construct($building_type, $building_id = false)
	{
		$bldg_class_name = Colony_Building::$types[$building_type] .'_building';
		require(WEBROOT .'classes/'. $bldg_class_name .'.php');
		$building_object = new $bldg_class_name($building_id);
		foreach ( $building_object as $field => $value )
			$this->$field = $value;
	}
	
	public function upgrade_cost()
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->upgrade_duration) )
			return $this->building_object->upgrade_duration();
		else
			return new Resource_Bundle(20,20,20,20);
	}
	
	public function upgrade_duration()
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->upgrade_duration) )
			return $this->building_object->upgrade_duration();
		else
			return $this->level * 70;
	}
	
	// This function gets called whenever this building gets upgraded.
	public function begin_upgrade()
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->begin_upgrade) )
			return $this->building_object->begin_upgrade();
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade()
	{
		// Deferr execution of this function to the specific building's object.
		if ( function_exists($this->building_object->finish_upgrade) )
			return $this->building_object->finish_upgrade();
		else
			$this->level++;
	}
}

?>