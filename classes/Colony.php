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
		require(WEBROOT .'classes/Colony_Building.php');
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
	
	// Saves this object's data into the database for persistent storage.
	public function save_data()
	{
		global $Mysql;
		
		$qry_str = "UPDATE `colonies` SET ";
		foreach ( $this as $field => $value )
		{
			if ( $field == 'resources' )
			{
				// Construct the resource portion of the query string.
				$i=1;
				foreach ( $this->resources as $resource )
				{
					$qry_str .= " `resource". $i ."_capacity` = '". $resource->capacity ."', ";
					$qry_str .= " `resource". $i ."_stock` = '". $resource->stock ."', ";
					$qry_str .= " `resource". $i ."_consumption_rate` = '". $resource->consumption_rate ."', ";
					$qry_str .= " `resource". $i ."_production_rate` = '". $resource->production_rate ."', ";
					$i++;
				}
			}
			else
			{
				$qry_str .= " `". $field ."` = '". $value ."', ";
			}
			
		}
		// remove the very last comma in the query string.
		$qry_str = substr($qry_str, 0, -2);
		
		$qry_str .= " WHERE `id`= '". $this->id ."'";
		// Run the constructed query to update a colony's resources.
		$Mysql->query($qry_str);
		//echo $qry_str;
	}
	
	// Increase this colony's resources by the given amount and save to database.
	// $resource_bundle is an object of type Resource_Bundle.
	public function add_resources($resource_bundle)
	{
		
		$this->update_resources();
		$this->save_data();
	}
	
	// Decrease this colony's resources by the given amount and save to database.
	// $resource_bundle is an object of type Resource_Bundle.
	public function subtract_resources($resource_bundle)
	{
		foreach ( $resource_bundle as $key => $value )
			$resource_bundle->$key *= -1;
		$this->add_resources($resource_bundle);
	}
	
	public function update_resources()
	{
		global $Mysql;
		
		// TODO: Make a way for newly-completed building upgrades to change this calculation.
		$seconds_passed = time() - $this->last_resource_update;
		$hours_passed = $seconds_passed / 3600;
		$this->resources->update($hours_passed);
		
		// Save the updated resource values to the database.
		// To reduce CPU load, this is only run at most every 15 minutes for a givev player.
		if ( $hours_passed > 0.25 )
		{
			$this->save_data();
		}
	}
	
	// This function gets called when a user click the button to upgrade a building.
	public function upgrade_building_begin($building_type)
	{
		$building = new Colony_Building($building_type);
		$this->subtract_resources($building->upgrade_cost());
		
		// Clean up
		unset($building);
	}
	
	// This function gets called when the upgrade job completes.
	public function upgrade_building_finish($building_type)
	{
		$building = new Colony_Building($building_type);
		
		// Clean up
		unset($building);
	}
}

?>