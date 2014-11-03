<?php

class Map_Tile extends Database_Row
{
	// Fields taken directly from the database:
	public $id, $coord_x, $coord_y, $object_count;
	
	// Extra fields:
	public $objects;
	protected $db_table_name = 'map_tiles';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'objects');
	
	function __construct($tileID_or_coords)
	{
		if ( is_array($tileID_or_coords) )
		{
			// This tile has not yet been allocated in the database.
			// Create it from scratch.
			$this->coord_x = $tileID_or_coords['x'];
			$this->coord_y = $tileID_or_coords['y'];
			$this->generate_elements();
			$this->save_data();
		}
		else
		{
			$this->id = $tileID_or_coords;
			$this->fetch_data();
		}
	}
	
	private function generate_elements()
	{
		$rand1 = mt_rand(1,10);
		$this->objects = array();
		
		if ( $rand1 <= 4 )
		{
			// Generate a single object for this tile.
			array_push($this->objects, $this->random_object());
		}
		else if ( false ) //$rand1 <= 5 )
		{
			// Generate two objects for this tile.
			array_push($this->objects, $this->random_object(), $this->random_object());
		}
		else
		{
			// generate no objects for this tile
			array_push($this->objects, $this->random_object(5,5));
		}
		
		foreach ( $this->objects as $new_object )
		{
			// Insert these world-objects into the DB.
			$new_object->save_data();
		}
		$this->object_count = count($objects);
	}

	private function random_object($min = 0, $max = 4) {
		$obj_type = mt_rand($min,$max);
		//$space_object = 

		return $space_object;
	}
}

?>