<?php

class Fleet extends Database_Row
{
	// Static data:
	public static $objectives1 = array('attack', 'hold_position', 'scout', 
			'merge', 'collect_resources', 'supply');
	public static $objectives2 = array('inflict_max_damage', 'defend_object', 
			'attack_visitors', 'pacifist', 'attack_if_attacked', 'monitor',
			'steal_resources');
	
	// Data taken directly from the "fleets" database table:
	public $id, $owner, $current_x_coord, $current_y_coord, $home_x_coord, 
		$home_y_coord, $primary_objective, $secondary_objective, $speed;
	
	// Additional data.
	public $stats, $ships;
	
	protected $db_table_name = 'fleets';
	protected $extra_fields = array('extra_fields','db_table_name', 
			'objectives1', 'objectives2', 'stats', 'ships');
	
	function __construct($id_or_db_row)
	{
		if ( !is_array($id_or_db_row) )
		{
			$id = $id_or_db_row;
			$this->id = $id;
			$this->fetch_data();
		}
		else
		{
			$db_row = $id_or_db_row;
			foreach ( $db_row as $field => $val )
			{
				$this->$field = $val;
			}
		}
	}
	
	// Returns an array of Fleet_Ship objects for the specified $fleet_id
	public function get_ships()
	{
		return Fleet_Ship::get_ships_in_fleet($this->id);
	}
	
	public function save_ships()
	{
		return Fleet_Ship::set_ships_in_fleet($this->id, $this->ships);
	}
	
	// Returns an array of fleet objects
	public static function fleets_at($x, $y)
	{
		global $Mysql;
		
		$fleets = array();
		$qry = $Mysql->query("SELCT * FROM `fleets` 
			WHERE `x_coord` = '". $x ."' AND `y_coord` = '". $y ."'");
		while ( $fleet_row = $qry->fetch_assoc )
			$fleets[] = new Fleet($fleet_row);
		
		return $fleets;
	}
	
	// This function computes and saves many of the stats and pre-calculations
	// that this fleet will need for battle calculations.
	public function battle_prep()
	{
		if ( empty($this->ships) )
			$this->ships = $this->get_ships();
		
		// Make some ship objects for stat references.
		$ref_ships = array();
		foreach ( $i = 0; $i < count(Ship::$types); $i++ )
			$ref_ships[$i] = Ship::construct_child(['type' => $1]);

		// List of stat names to tally.
		$stat_names = array('atk', 'def', 'shield', 'hp');
		
		// Tally ship stats.
		foreach ( $this->ships as $fship )
		{
			// Get a reference to the generic ship object with ship stats for this type of ship.
			$ship = $ref_ships[$fship->type];

			$this->stats = array();
			foreach ( $stat_names as $stat_name )
				$this->stats[$stat_name] += ($ship->$stat_name * $fship->count);
		}
	}
}

?>