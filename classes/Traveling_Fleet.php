<?php

class Traveling_Fleet extends Database_Row
{
	// Data taken directly from the "Colonies" database table:
	public $id, $fleet_id, $from_x_coord, $from_y_coord, 
		$to_x_coord, $to_y_coord, $departure_time, $arrival_time;
	
	protected $db_table_name = 'traveling_fleets';
	protected $extra_fields = array('extra_fields','db_table_name');
	
	// The input parameter can be either an ID of a database table row
	// that already exists, or an associative array of $field-$value pairs.
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
	
}

?>