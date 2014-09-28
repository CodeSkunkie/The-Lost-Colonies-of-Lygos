<?php

class Food_Building extends Colony_Building
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type, $level;
	
	// Extra fields:
	public $name = 'Meat Lands';
	public $long_descript = 'AKA: "Space-Pasture". Food is grown here to feed everyone in this colony.';
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
				$this->type = 2;
		}
	}
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(
			10 * $this->level + 15,
			10 * $this->level + 35,
			10 * $this->level + 20,
			10 * $this->level + 20);
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade($colony)
	{
		$this->level++;
	}
	
	public function upgrade_duration()
	{
		return 20;
	}
}

?>
