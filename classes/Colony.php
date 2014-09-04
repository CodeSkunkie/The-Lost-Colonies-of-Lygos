<?php

class Colony
{
	// Data taken directly from the "Colonies" database table:
	public $id, $player_id, $tile_id, $last_resource_update, $resources;
	// $resources is an object with the form:
	//		$resources->resource1->stock where 'stock' can be
	//		'stock', 'capacity', 'production_rate', or 'consumption_rate', and
	//		'resource1' can be replaced by an alias such as 
	//		'food', 'water', 'metal', or 'energy'.
	
	function __construct($id)
	{
		require(WEBROOT .'classes/Colony_Resources.php');
		require(WEBROOT .'classes/Colony_Resource.php');
		$this->id = $id;
		$this->fetch_data();
	}
	
	
	// Retrieves some of the colony's data from the database.
	// $this->id must be set before this function can be called.
	public function fetch_data()
	{
		global $Mysql;
		
		$colony_qry = $Mysql->query("SELECT * FROM `colonies` 
			WHERE `id`=". $this->id);
		$colony_qry->data_seek(0);
		$colony_row = $colony_qry->fetch_assoc();

		
		foreach ( $colony_row as $field => $value )
			$this->$field = $value;
		
		$this->resources = new Colony_Resources($colony_row);
	}
	
	public function update_resources()
	{
		global $Mysql;
		
		// TODO: Make a way for newly-completed building upgrades to change this calculation.
		$seconds_passed = time() - $this->last_resource_update;
		$hours_passed = $seconds_passed / 3600;
		$this->resources->update($hours_passed);
		
		// Construct a mysql query to update the DB's resource values.
		// To reduce CPU load, this is only run at most every 15 minutes for a givev player.
		if ( $hours_passed > 0.25 )
		{
			$qry_str = "UPDATE `colonies` SET ";
			$i=1;
			foreach ( $this->resources as $resource )
			{
				$qry_str .= " `resource". $i ."_stock` = '". $resource->stock ."', ";
				$i++;
			}
			$qry_str .= " `last_resource_update` = ". time() ."
				WHERE `id`= '". $this->id ."'";
			// Run the constructed query to update a colony's resources.
			$Mysql->query($qry_str);
		}
	}
}

?>