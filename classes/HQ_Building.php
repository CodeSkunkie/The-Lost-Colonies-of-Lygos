<?php

class HQ_Building extends Colony_Building
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type, $level;
	
	// Extra fields:
	public $name = 'Headquarters';
	public $long_descript = "This is your colony's command center and crews' living quarters.";
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
			$this->id = $id_or_db_row;
			$this->fetch_data();
		}
	}
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(
			10 * $this->level,
			20 * $this->level,
			60 * $this->level,
			40 * $this->level);
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade()
	{
		$this->level++;
	}
}

?>