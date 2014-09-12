<?php

class Map_Tile extends Database_Row
{
	// Fields taken directly from the database:
	public $id, $coord_x, $coord_y, $object_count;
	
	// Extra fields:
	public $objects;
	protected $db_table_name = 'map_tiles';
	protected $extra_fields = array('db_table_name', 'objects');
	
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
		
		if ( false ) //$rand1 <= 4 )
		{
			// Generate a single object for this tile.
			$obj_type = mt_rand(1,5);
			$this->objects[] = new World_Object();
		}
		else if ( false ) //$rand1 <= 5 )
		{
			// Generate two objects for this tile.
		}
		else
		{
			// generate no objects for this tile
		}
		
		foreach ( $this->objects as $new_object )
		{
			// Insert these world-objects into the DB.
			
		}
		$this->object_count = count($objects);
	}
}

?>