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
	public static function get_ships_in_fleet($fleet_id)
	{
		global $Mysql;
		
		$results = array();
		$qry = $Mysql->query("SELCT * FROM `fleet_ships` 
			WHERE `fleet_id` = '". $fleet_id ."' 
			ORDER BY `type` ASC ");
		while ( $db_row = $qry->fetch_assoc )
			$results[] = new Fleet_Ship($db);
		
		return $results;
	}
	
	// 
	public static function set_ships_in_fleet($fleet_id, $fships)
	{
		global $Mysql;
		
		$qry = "";
		foreach ( $fships as $fship )
		{
			if ( $fship->fleet_id != $fleet_id )
				return false;
			
			if ( $fship->count == 0 )
			{
				$qry .= " DELETE FROM `fleet_ships` 
					WHERE `id` = '". $fship->id ."';\n";
			}
			else
			{
				$qry .= " UPDATE `fleet_ships` 
					SET `count` = '". $fship->count ."'
					WHERE `id` = '". $fship->id ."' ;\n";
			}
		}
		$qry = $Mysql->query($qry);
	}
}

?>