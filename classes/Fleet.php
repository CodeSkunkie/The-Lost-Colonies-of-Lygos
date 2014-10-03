<?php

class Fleet extends Database_Row
{
	// Data taken directly from the "Colonies" database table:
	public $id, $owner, $current_x_coord, $current_y_coord, $home_x_coord, 
		$home_y_coord, $primary_objective, $secondary_objective, $speed;
	
	protected $db_table_name = 'fleets';
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
	
	public function get_ships()
	{
		return Fleet_Ship::get_ships_in_fleet($this->id);
	}
	
	// Returns an array of fleet objects
	public static function fleets_at($x, $y)
	{
		global $Mysql;
		
		$fleets = array();
		$qry = $Mysql->query("SELCT * FROM `fleets` 
			WHERE `x_coord` = '". $x ."' AND `y_coord` = '". $y ."'");
		while ( $fleet_row = $qry->fetch_assoc )
			$fleets[] = new Fleet($fleet_row);
		
		return $fleets;
	}
}

?>