<?php

class Colony_Building
{
	public $id, $colony_id, $type, $level;
	public $name;
	
	function __construct($type)
	{
		$names = array('command center', 'energy collector');
	
		$this->type = $type;
		//$this->name = $names[$type];
	}
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(10,20,30,40);
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade()
	{
		$resource1_rate += $this->level * 12;
		$this->level++;
	}
	
	// Retrieves this object's data from the database.
	public function fetch_data()
	{
		
	}
	
	// Saves this object's data to the database.
	public function save_data()
	{
	
	}
}

?>