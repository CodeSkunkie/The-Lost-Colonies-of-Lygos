<?php

class HQ_Building extends Colony_Building
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type =0, $level;
	
	// Extra fields:
	public $name = 'Headquarters';
	public $long_descript = "This is your colony's command center and crews' living quarters.";
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'name', 'long_descript', 'extra_fields', 'building_object');
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(
			10 * $this->level + 5,
			20 * $this->level + 5,
			50 * $this->level + 25,
			35 * $this->level + 15);
	}
	
	public function upgrade_duration()
	{
		return $this->level * 30;
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade($colony)
	{
		$this->level++;
	}
}

?>
