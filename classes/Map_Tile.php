<?php

class Map_Tile
{
	public $id, $coord_x, $coord_y, $object_count;
	public $objects;
	
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
	
	// Retrieves this object's data from the database.
	public function fetch_data()
	{
		global $Mysql;
		
		$tiles_qry = $Mysql->query("SELECT * FROM `map_tiles` 
			WHERE `id` = '". $this->id ."'");
		$tiles_qry->data_seek(0);
		$tile_row =  $tiles_qry->fetch_assoc();
		
		foreach ( $tile_row as $field => $value )
			$this->$field = $value;
	}
	
	// Saves this object's data to the database.
	public function save_data()
	{
		global $Mysql;
		
		if ( !empty($this->id) )
		{
			// Update this pre-existing tile.
			$qry_str_part1 = "UPDATE `map_tiles` SET ";
			$qry_str_part3 = " WHERE `id`= '". $this->id ."'";
		}
		else
		{
			// This tile does not exist in the database yet. Create it.
			$qry_str_part1 = "INSERT INTO `map_tiles` SET ";
			$qry_str_part3 = "";
		}
		$qry_str_part2 = "";
		foreach ( $this as $field => $value )
		{
			if ( $field == 'id' || $field == 'objects' )
				continue;
			$qry_str_part2 .= " `". $field ."` = '". $value ."', ";
		}
		// remove the very last comma in the query string.
		$qry_str_part2 = substr($qry_str_part2, 0, -2);
		
		// Run the constructed query to update a colony's resources.
		$qry_str = $qry_str_part1 . $qry_str_part2 . $qry_str_part3;
		$Mysql->query($qry_str);
		
		// If we just created a new tile, retrieve its newfound id from the DB.
		if ( empty($this->id) )
			$this->id = $Mysql->insert_id;
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