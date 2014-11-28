<?php

abstract class World_Object extends Database_Row
{
	// Static class stuff:
	// TODO: add things to the below $types array. Elements should be strings that help identify
	//		the class names of children classes. For example, if you add a world object class
	//		named 'Asteroid_World_Object', you would simply add "Asteroid" to this array.
	// DO NOT RE-ORDER THIS ARRAY. DO NOT DELETE ELEMENTS. APPEND TO END ONLY.
	public static $types = array('Asteroid', 'Planet', 'Star', 'Wreckage', 'NPC_Random', 'Empty');
	public static $outer_rim = 400;

	// Fields taken directly from the database:
	public $id, $type, $x_coord, $y_coord, $owner;
	protected $mass = -1; // -1 for inexhaustible, immobile resources. May be changed in child classes

	// Extra fields:
	// TODO: Add any extra fields you need, but make sure to add the field
	//		name to the $extra_fields array too.
	public $name;
	public $long_descript;
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'name', 'long_descript');

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
		if ( !isset($fields['type']) || !isset(self::$types[$fields['type']]) )
			return NULL;

		$wo_class_name = self::type2classname($fields['type']);
		load_class($wo_class_name);
		return new $wo_class_name($fields, $fetch_data);
	}

	// this function should be called whenever a scout ship reaches a tile
	public static function scout($x, $y) {
		global $Mysql;
		$qry = $Mysql->query("SELECT * FROM `world_objects` 
			WHERE `x_coord` = '". $x ."' AND `y_coord` = '". $y ."'");

		if (($qry->num_rows) == 0) {
			return self::random_object($x, $y);
		} else {
			return self::objects_at($x, $y, $qry);
		}
	}

	// temporary static function for generating random world object
	public static function random_object($x, $y) {
		$percent = self::dropoff(hex_distance(0,0,$x,$y));

		$rand1 = mt_rand(1,10);
		$random_objects = array();
		
		if ( $rand1 <= (5 * $percent) )
		{
			// Generate a single object for this tile.
			$random_objects[] = self::build_object($x, $y, mt_rand(0,4));
		}
		else
		{
			// generate no objects for this tile
			$random_objects[] = self::build_object($x, $y, 5);
		}
		
		foreach ( $random_objects as $new_object )
		{
			// Insert these world-objects into the DB.
			$new_object->save_data();
		}

		return $random_objects;
	}

	public static function build_object($x, $y, $new_type) {
		$space_object = self::construct_child([
			'type' => $new_type,
			'x_coord' => $x,
			'y_coord' => $y
			]);
		return $space_object;
	}
	
	// This function uses some ellipse math to determine the gradual sparcity of
	// world objects as a function of distance from the center of the map.
	public static function dropoff($dist) {
		if ($dist <= self::$outer_rim) return 1;

		// Any tiles beyond the input distance times this multiplyer are garunteed to be empty.
		$absolute_void_multiplier = 4;

		$radicand = 1 - pow((($dist - self::$outer_rim)/(self::$outer_rim / $absolute_void_multiplier)), 2);

		if ($radicand <= 0) {
			return 0;
		}

		return sqrt($radicand);
	}
	
	// shamelessly borrowed from colony_building
	function __construct($fields, $fetch_data = true)
	{
		foreach ( $fields as $field => $value )
			$this->$field = $value;

		// Grab all field data from the database if specified.
		if ( $this->exists() && $fetch_data )
			$this->fetch_data();
	}

	protected function destroy()
	{
		global $Mysql;
		$job_qry = $Mysql->query("DELETE FROM `world_objects`
				WHERE `id` = '". $this->id ."' ");
		$this->id = false;
	}

	// Returns an array of world objects from DB
	public static function objects_at($x, $y, $qry = false)
	{
		global $Mysql;

		$objects = array();

		if (!$qry) {
			$qry = $Mysql->query("SELECT * FROM `world_objects` 
				WHERE `x_coord` = '". $x ."' AND `y_coord` = '". $y ."'");
			if (($qry->num_rows) == 0) {
				return $objects;
			}
		}

		while ( $object_row = $qry->fetch_assoc() ) {
			$wo_class_name = self::type2classname($object_row['type']);
			load_class($wo_class_name);
			$objects[] = new $wo_class_name($object_row, false);
		}
		return $objects;
	}
	
	// If a world object is to return resources to raid parties, it should
	// implement this method and return some resources based on the inputted
	// cargo capacity of the raiding party.
	public function yield_resources($fleet_capacity)
	{
		return new Resource_Bundle(0,0,0,0);
	}
	
	public static function error_out($obj, $notifier) {
		$str = "\r\n|=====NEW STUFF STARTS HERE=====|";
		file_put_contents('php://stderr', print_r($str, TRUE));
		file_put_contents('php://stderr', print_r($notifier, TRUE));
		file_put_contents('php://stderr', print_r("\r\n", TRUE));
		file_put_contents('php://stderr', print_r($obj, TRUE));
	}
}

?>