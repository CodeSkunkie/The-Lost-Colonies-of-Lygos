<?php

class Job extends Database_Row
{
	// The types here should match the class names for creating objects.
	public static $types = array('Colony_Building','Ship','Research_Item','Fleet');

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
			// Make a generic object for a product that doesn't yet exist.
			if ( Job::$types[$job_type] == 'Colony_Building' )
			{
				$building = Colony_Building::construct_child([
					'type' => $product_type,
					'level' => 0,
					'colony_id' => $colony_id
				]);

				return $building;
			}
			else if ( Job::$types[$job_type] == 'Ship' )
			{
// TODO: (TJ)
//				$building = Ship::construct_child([
//					'type' => $product_type,
//					'level' => 0,
//					'colony_id' => $colony_id
//				]);
			}
			else if ( Job::$types[$job_type] == 'Research_Item' )
			{
// TODO: (Allen)
//				$building = Ship::construct_child([
//					'type' => $product_type,
//					'level' => 0,
//					'colony_id' => $colony_id
//				]);
			}
			else if ( Job::$types[$job_type] == 'Fleet' )
			{
				// This shouldn't happen because fleets aren't
				// constructed by jobs.
			}
		}
		else
		{
			// Make an ojbect for a pre-existing product.
			if ( Job::$types[$job_type] == 'Colony_Building' )
			{
				return Colony_Building::construct_child([
					'type' => $product_type,
					'id' => $product_id
				]);
			}
			else if ( Job::$types[$job_type] == 'Ship' )
			{
				// This would only be used if a ship gets upgraded directly.
				// It should be upgraded through research items instead though.
			}
			else if ( Job::$types[$job_type] == 'Research_Item' )
			{
// TODO: (Allen)
//				return Research_Item::construct_child([
//					'type' => $product_type,
//					'id' => $product_id
//				]);
			}
			else if ( Job::$types[$job_type] == 'Fleet' )
			{
				return new Fleet($product_id);
			}
		}
	}
}

?>