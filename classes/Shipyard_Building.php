<?php

class Shipyard_Building extends Colony_Building
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type =7, $level;
	
	// Extra fields:
	public $name = 'Shipyard';
	public $long_descript = 'This is where all of your ships are kept. You can also create new ships here.';
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'name', 'long_descript', 'extra_fields', 'building_object');
	
	
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
