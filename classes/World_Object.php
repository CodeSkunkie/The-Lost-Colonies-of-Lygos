<?php

abstract class World_Object extends Database_Row
{
	// Static class stuff:
	// TODO: add things to the below $types array. Elements should be strings that help identify
	//		the class names of children classes. For example, if you add a world object class
	//		named 'Asteroid_World_Object', you would simply add "Asteroid" to this array.
	// DO NOT RE-ORDER THIS ARRAY. DO NOT DELETE ELEMENTS. APPEND TO END ONLY.
	public static $types = array(); 
	// Given a research item type number, return its class name.
	public static function type2classname($type)
	{
		return ( self::$types[$type] .'_World_Object');
	}
	// Static constructor for constructing children based on type:
	// The input parameter $fields is an associative array of $field-$value pairs
	// that should correspond to field names in the child object.
	// $fields['type'] must ALWAYS be specified.
	// If input parameter $fetch_data is false, no data will be retrieved 
	// from the database for this object. Fetching also requires $fields['id'].
	public static function construct_child($fields, $fetch_data = true)
	{
		if ( !isset($fields['type']) || !isset(Research_Item::$types[$fields['type']]) )
			return NULL;
		
		$wo_class_name = World_Object::type2classname($fields['type']);;
		load_class($wo_class_name);
		return new $wo_class_name($fields, $fetch_data);
	}

	// Fields taken directly from the database:
	public $id, $type, $x_coord, $y_coord, $owner;
	
	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field 
	//		name to the $extra_fields array too.
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields');
	
	// TODO: What methods might world objects need?
}

?>