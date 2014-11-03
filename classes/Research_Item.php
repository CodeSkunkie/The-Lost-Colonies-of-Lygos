<?php

abstract class Research_Item extends Database_Row
{
	// Static class stuff:
	// TODO: add things to the below $types array. Elements should be strings that help identify
	//		the class names of children classes. For example, if you add a research item class
	//		named 'Shield_Research_Item', you would simply add "Shield" to this array.
	// DO NOT RE-ORDER THIS ARRAY. DO NOT DELETE ELEMENTS. APPEND TO END ONLY.
	public static $types = array('Fighter', 'Scout', 'Tank', 'Cargo'); 
	// Given a research item type number, return its class name.
	public static function type2classname($type)
	{
		return ( $type .'_Research');
	}
	// Static constructor for constructing children buildings:
	// The input parameter $fields is an associative array of $field-$value pairs.
	// $fields['type'] must ALWAYS be specified.
	// If input parameter $fetch_data is false, no data will be retrieved 
	// from the database for this object. Fetching also requires $fields['id'].
	public static function construct_child($fields, $fetch_data = true)
	{
		if ( !isset($fields['type']))
			return NULL;
		
		//$research_class_name = Research_Item::type2classname($fields['type'].'_Research');;
		//$research_class_name = $fields['type'];
		//$research_class_name = Research_Item::$types[$fields['type']];
		$research_class_name = Research_Item::type2classname($fields['type']);;
		load_class($research_class_name);
		return new $research_class_name($fields, $fetch_data);
		//return $research_class_name;
	}

	public static function construct_child_num($fields, $fetch_data = true)
	{
		if ( !isset($fields['type']))
			return NULL;
		$research_class_num = Research_Item::$types[$fields['type']];
		$research_class_name = Research_Item::type2classname($research_class_num);;
		load_class($research_class_name);
		return new $research_class_name($fields, $fetch_data);
		//return $research_class_name;
	}
	
	// Fields taken directly from the database:
	public $id, $player_id, $type, $level;
	
	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field 
	//		name to the $extra_fields array too.
	public $name;
	public $long_descript;
	protected $db_table_name = 'research';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript');
	
	public function research_cost()
	{
		return new Resource_Bundle(
			5 * $this->level + 20,
			5 * $this->level + 20,
			7 * $this->level + 20,
			5 * $this->level + 20);
		// TODO: implement this function in each child class to customize per research item.
	}
	
	public function research_duration()
	{
		return 3600 + ($this->level * 1.75); // seconds
		// TODO: implement this function in each child class to customize per research item.
	}
	
	// This function gets called whenever a research item of the 
	// child-type is researched.
	// $colony should be a reference to this building's colony object.
	public function finish_upgrade($colony)
	{
		// TODO: implement this function in each child class.
	}
}

?>