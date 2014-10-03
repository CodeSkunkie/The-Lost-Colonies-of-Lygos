<?php

class Colony
{
	// Data taken directly from the "Colonies" database table:
	public $id, $player_id, $x_coord, $y_coord, $last_resource_update, $resources;
	// $resources is an object with the form:
	//		$resources->resource1->stock where 'stock' can be
	//		'stock', 'capacity', 'production_rate', or 'consumption_rate', and
	//		'resource1' must be replaced by an alias such as 
	//		'food', 'water', 'metal', or 'energy'.
	
	function __construct($id)
	{
		load_class('Colony_Resource');
		load_class('Colony_Resources');
		
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

		// This loop is going through all DB fields instead of object fields.
		// The resource fields are being pulled from their raw fields.
		foreach ( $this as $field => $value )
		{
			if ( $field == 'resources' )
				continue;
			
			$this->$field = $colony_row[$field];
		}
		//print_arr($this);
		$this->resources = new Colony_Resources($colony_row);
	}
	
	// Saves this object's data into the database for persistent storage.
	public function save_data()
	{
		global $Mysql;
		
		$qry_str = "UPDATE `colonies` SET ";
		foreach ( $this as $field => $value )
		{
			// Never change the row ID!
			if ( $field == 'id' )
				continue;
			
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
		//nechon( $qry_str);
	}
	
	// Increase this colony's resources by the given amount.
	// DOES NOT SAVE TO THE DATABASE for reasons of efficiency.
	// $resource_bundle is an object of type Resource_Bundle.
	public function add_resources($resource_bundle)
	{
		$this->update_resources();
		foreach ( $resource_bundle as $key => $val )
		{
			$this->resources->$key->stock += $val;
			
			// Don't let the stock quantity go below zero.
			if ( $this->resources->$key->stock < 0 )
				$this->resources->$key->stock = 0;
		}
	}
	
	// Decrease this colony's resources by the given amount.
	// DOES NOT SAVE TO THE DATABASE for reasons of efficiency.
	// $resource_bundle is an object of type Resource_Bundle.
	public function subtract_resources($resource_bundle)
	{
		foreach ( $resource_bundle as $key => $value )
			$resource_bundle->$key *= -1;
		
		$this->add_resources($resource_bundle);
	}
	
	// Increase this colony's resources by the given amount.
	// DOES NOT SAVE TO THE DATABASE for reasons of efficiency.
	// $resource_bundle is an object of type Resource_Bundle.
	public function can_afford($resource_bundle)
	{
		foreach ( $resource_bundle as $key => $val )
		{
			if ( $this->resources->$key->stock - $val < 0 )
				return false;
		}
		
		return true;
	}
	
	
	// DOES NOT SAVE CHANGES TO THE DATABASE for reasons of efficiency.
	public function update_resources()
	{
		global $Mysql;
		
		// TODO: Make a way for newly-completed building upgrades to change this calculation.
		$seconds_passed = time() - $this->last_resource_update;
		$hours_passed = $seconds_passed / 3600;
		$this->resources->update($hours_passed);
		$this->last_resource_update = time();
	}
	
	// This function gets called when a user click the button to upgrade a building.
	public function upgrade_building_begin($building_type)
	{
		$building = Colony_Building::construct_child(['type' => $building_type]);
		$this->subtract_resources($building->upgrade_cost());
		
		// Clean up
		unset($building);
	}
	
	// This function gets called when the upgrade job completes.
	public function upgrade_building_finish($building_type)
	{
		$building = Colony_Building::construct_child(['type' => $building_type]);
		
		// Clean up
		unset($building);
	}
}

?>