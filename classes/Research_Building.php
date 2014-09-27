<?php

class Research_Building extends Colony_Building
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type, $level;
	
	// Extra fields:
	public $name = 'Research Labs';
	public $long_descript = 'Scientists work around the clock with whatever resources you give them to develop new technology.';
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'name', 'long_descript', 'extra_fields', 'building_object');
	
	function __construct($id_or_db_row)
	{
		if ( is_array($id_or_db_row) )
		{
			foreach ( $id_or_db_row as $field => $value )
				$this->$field = $value;
			$this->save_data();
		}
		else
		{
			$this->id = $id_or_db_row;
			$this->fetch_data();
			if ( !$this->exists() )
				$this->type = 5;
		}
	}
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(10,20,30,40);
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade($colony)
	{
		$this->level++;
	}
}

?>