<?php

class Colony_Building extends Database_Row
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type, $level;
	
	// Extra fields:
	public $name;
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'objects');
	
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
}

?>