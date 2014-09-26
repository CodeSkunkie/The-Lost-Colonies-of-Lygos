<?php

class World_Object extends Database_Row
{
	// Fields taken directly from the database:
	public $id, $type, $x_coord, $y_coord, $owner;
	
	// Extra fields:
	protected $db_table_name = 'world_objects';
	protected $extra_fields = array('db_table_name');
	
	// This constructor queries the database to create the tile.
	function __construct($tile_id)
	{
		$this->id = $tile_id;
		$this->fetch_data();
	}
}

?>