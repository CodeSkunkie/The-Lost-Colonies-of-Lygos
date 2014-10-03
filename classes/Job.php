<?php

class Job extends Database_Row
{
	// The types here should match the class names for creating objects.
	public static $types = array('Colony_Building','Ship','Research_Item','Traveling_Fleet');

	// Fields taken directly from the database:
	public $id, $colony_id, $type, $product_id, $product_type, $start_time, $completion_time, $duration, $repeat_count;
	
	// Extra fields:
	protected $db_table_name = 'job_queue';
	protected $extra_fields = array('db_table_name', 'extra_fields', 'types');
	
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
	
	public static function make_product_object($job_type, $product_id, $product_type, $colony_id)
	{
		if ( $product_id == 0 )
		{
			// Make an ojbect for a product that doesn't yet exist
			// and then save it into existence.
			if ( $job_type == 0 )
			{
				// Create a building object for a brand-new building.
				// Note: Building's level will be incremented when finish_upgrade() is called.
				$building = Colony_Building::construct_child([
					'type' => $product_type,
					'level' => 0,
					'colony_id' => $colony_id
				]);
				// Insert this new building into the DB.
				// Note: This will also give the building object an id.
				$building->save_data();
				return $building;
			}
		}
		else
		{
			// Make an ojbect for a pre-existing product.
			if ( $job_type == 0 )
			{
				return Colony_Building::construct_child([
					'type' => $product_type,
					'id' => $product_id
				]);
			}
		}
	}
}

?>