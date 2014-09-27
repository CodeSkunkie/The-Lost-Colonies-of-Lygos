<?php

abstract class Database_Row
{
	// Any class fields that don't correspond to a database table field of
	// the same name should be recorded here.
	protected $extra_fields = array();

	// The name of the database table must be specified.
	protected $db_table_name;
	
	
	// Retrieves this object's data from the database.
	public function fetch_data()
	{
		global $Mysql;
		
		// Don't bother if we know there is no row for this object.
		if ( !$this->exists() )
			return 0;
		
		$table_qry = $Mysql->query("SELECT * FROM `". $this->db_table_name ."` 
			WHERE `id` = '". $this->id ."'");
		if ( $table_qry->num_rows == 0 )
			$this->id = false;
		else
		{
			$table_qry->data_seek(0);
			$table_row =  $table_qry->fetch_assoc();
			
			foreach ( $table_row as $field => $value )
			{
				// Skip over any of the non-database fields.
				if ( in_array($field, $this->extra_fields) )
					continue;
				
				$this->$field = $value;
			}
		}
	}
	
	// Saves this object's data to the database.
	public function save_data()
	{
		global $Mysql;
		
		if ( $this->exists() )
		{
			// Update this pre-existing tile.
			$qry_str_part1 = "UPDATE `". $this->db_table_name ."` SET ";
			$qry_str_part3 = " WHERE `id`= '". $this->id ."'";
		}
		else
		{
			// This object does not exist in the database yet. Create it.
			$qry_str_part1 = "INSERT INTO `". $this->db_table_name ."` SET ";
			$qry_str_part3 = "";
		}
		$qry_str_part2 = "";
		foreach ( $this as $field => $value )
		{
			// Skip over any of the non-database fields.
			if ( in_array($field, $this->extra_fields) )
				continue;
			// Never change the row ID!
			if ( $field == 'id' )
				continue;
			
			$qry_str_part2 .= " `". $field ."` = '". $value ."', ";
		}
		// remove the very last comma in the query string.
		$qry_str_part2 = substr($qry_str_part2, 0, -2);
		
		// Run the constructed query to update a colony's resources.
		$qry_str = $qry_str_part1 . $qry_str_part2 . $qry_str_part3;
		$Mysql->query($qry_str);
		//echon($qry_str);
		
		// If we just created a new row, retrieve its newfound id from the DB.
		if ( !$this->exists() )
		{
			$this->id = $Mysql->insert_id;
		}
	}
	
	// Returns boolean true/false to indicate if 
	// the fetch_data operation was successful.
	public function exists()
	{
		if ( empty($this->id) || $this->id === false )
			return false;
		else
			return true;
	}
}

?>