<?php

class Job extends Database_Row
{
	// Fields taken directly from the database:
	public $id, $colony_id, $building_id, $building_type, $start_time, $completion_time;
	
	// Extra fields:
	protected $db_table_name = 'job_queue';
	protected $extra_fields = array('db_table_name', 'extra_fields');
	
	function __construct($id)
	{
		$this->id = $id;
		$this->fetch_data();
	}
	
	public function delete()
	{
		global $Mysql;
		
		$job_qry = $Mysql->query("DELETE FROM `job_queue`
				WHERE `id` = '". $this->id ."' ");
		$this->id = false;
	}
}

?>