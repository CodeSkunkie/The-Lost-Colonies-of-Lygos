<?php

class Water_Building extends Colony_Building
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type =1, $level;
	
	// Extra fields:
	public $name = 'H20 Synthesizer';
	public $long_descript = 'This module creates water for the colony using advanced molecular synthesis.';
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
