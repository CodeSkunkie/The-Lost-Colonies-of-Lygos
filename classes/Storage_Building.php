<?php

class Storage_Building extends Colony_Building
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type, $level;
	
	// Extra fields:
	public $name = 'Resource Reserves';
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name');
	
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
			$this->colony_id = $colony_id;
			$this->type = $type;
			$this->fetch_data();
		}
	}
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(10,20,30,40);
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade()
	{
		$this->level++;
	}
}

?>