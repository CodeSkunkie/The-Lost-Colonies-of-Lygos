<?php

class Fleet_Ship extends Database_Row
{
	// Data taken directly from the "Colonies" database table:
	public $id, $fleet_id, $type, $count, $special_orders;
	
	protected $db_table_name = 'fleet_ships';
	protected $extra_fields = array('extra_fields','db_table_name');
	
	function __construct($id_or_db_row)
	{
		if ( !is_array($id_or_db_row) )
		{
			$id = $id_or_db_row;
			$this->id = $id;
			$this->fetch_data();
		}
		else
		{
			$db_row = $id_or_db_row;
			foreach ( $db_row as $field => $val )
			{
				$this->$field = $val;
			}
		}
	}
	
	// Returns an array of Fleet_Ship objects for the specified $fleet_id.
	// TheaArray keys are the ship type.
	public static function get_ships_in_fleet($fleet_id)
	{
		global $Mysql;
		
		$results = array();
		$qry = $Mysql->query("SELECT * FROM `fleet_ships` 
			WHERE `fleet_id` = '". $fleet_id ."' 
			ORDER BY `type` ASC ");
		while ( $db_row = $qry->fetch_assoc() )
			$results[$db_row['type']] = new Fleet_Ship($db_row);
		
		return $results;
	}
	
	// Given an array of fships (where index = ship type), this function
	// will add them to the specified fleet in the database.
	public static function set_ships_in_fleet($fleet_id, $fships)
	{
		global $Mysql;
		
		$qry = "";
		// Iterate over all possible ship types.
		foreach ( $type = 0; $type < count(Ship::$types); $type++ )
		{
			// See if this ship type should be in the new fleet.
			if ( !isset($fships[$type]) )
			{
				// This fleet has/(no longer has) no ships of this type.
				$qry .= " DELETE FROM `fleet_ships` 
					WHERE `fleet_id` = '". $fleet_id ."' AND
						`type` = '". $type ."';\n";
			}
			else
			{
				// This fleet should have some ships of this type.
				$fship = $fships[$type];
				$qry .= "INSERT INTO `fleet_ships` 
					SET `fleet_id` = '". $fleet_id ."',
						`type` = '". $type ."',
						`count` = '". $fship->count ."'
					ON DUPLICATE KEY UPDATE
						`count` = '". $fship->count ."'\n";
			}
		}
		$qry = $Mysql->query($qry);
	}
}

?>