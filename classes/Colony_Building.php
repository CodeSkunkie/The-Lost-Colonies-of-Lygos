<?php

abstract class Colony_Building extends Database_Row
{
	// Static class stuff:
	public static $types = array('HQ', 'Water', 'Food', 'Metal', 'Energy', 'Research', 'Storage', 'Shipyard');
	// Given a building type number, return its class name.
	public static function type2classname($type)
	{
		return ( self::$types[$type] .'_Building');
	}
	
	// Fields taken directly from the database:
	public $id, $colony_id, $type, $level;
	
	// Extra fields:
	public $name;
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'objects');
	
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(10,20,30,40);
	}
	
	// This function gets called whenever this building gets upgraded.
	public function begin_upgrade()
	{
		
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade()
	{
		$this->level++;
	}
	
//	public function fetch_data()
//	{
//		global $Mysql;
//		
//		$colony_qry = $Mysql->query("SELECT * FROM `buildings` 
//			WHERE `colony_id` = '". $this->colony_id ."' AND
//				`type` = '". $this->type ."'" );
//		$colony_qry->data_seek(0);
//		$colony_row = $colony_qry->fetch_assoc();
//
//		
//		foreach ( $colony_row as $field => $value )
//			$this->$field = $value;
//	}
}

?>