<?php

class World_Object
{
	public $id, $type, $tile_id, $owner;
	
	// This constructor queries the database to create the tile.
	function __construct($tile_id)
	{
		$this->fetch_data();
	}
		
	// Retrieves this object's data from the database.
	public function fetch_data()
	{
		global $Mysql;
		
		$tiles_qry = $Mysql->query("SELECT * FROM `world_objects` 
			WHERE `id` = '". $this->id ."'");
		$tile_row = $tiles_qry->data_seek(0);
		
		foreach ( $tile_row as $field => $value )
			$this->$field = $value;
	}
	
	// Saves this object's data to the database.
	public function save_data()
	{
		global $Mysql;
		
		if ( !empty($this->id) )
		{
			$qry_str_part1 = "UPDATE `world_objects` SET ";
			$qry_str_part3 = " WHERE `id`= '". $this->id ."'";
		}
		else
		{
			$qry_str_part1 = "INSERT INTO `world_objects` SET ";
			$qry_str_part3 = "";
		}
		$qry_str_part2 = "";
		foreach ( $this as $field => $value )
		{
			if ( $field == 'id' )
				continue;
			$qry_str_part2 .= " `". $field ."` = '". $value ."', ";
		}
		// remove the very last comma in the query string.
		$qry_str_part2 = substr($qry_str_part2, 0, -2);
		
		// Run the constructed query to update a colony's resources.
		$qry_str = $qry_str_part1 . $qry_str_part2 . $qry_str_part3;
		$Mysql->query($qry_str);
		
		// If we just created a new world object, retrieve its newfound id.
		if ( empty($this->id) )
			$this->id = $Mysql->insert_id;
	}
}

?>