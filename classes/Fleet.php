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
		$home_y_coord, $primary_objective, $secondary_objective, $speed, 
		$traveling, $from_x_coord, $from_y_coord, 
		$to_x_coord, $to_y_coord, $departure_time, $arrival_time;
	
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
		// Within the same php script, ships will only change if
		// the script itself changes them, so only let this object
		// retrieve its ships once.
		
		if ( $this->ships == [] || !empty($this->ships) )
		{
			// The ships have already been retrieved or set manually.
		}
		else
		{
			load_class('Fleet_Ship');
			$this->ships = Fleet_Ship::get_ships_in_fleet($this->id);
			return $this->ships;
		}
	}
	
	public function save_ships()
	{
		if ( $this->ships == [] || !empty($this->ships) )
		{
			return Fleet_Ship::set_ships_in_fleet($this->id, $this->ships);
		}
		else
		{
			// Ships were never set. Do not save.
		}
	}
	
	// Returns an array of fleet objects
	public static function fleets_at($x, $y, $user_id = false)
	{
		global $Mysql;
		
		$fleets = array();
		$qry = $Mysql->query("SELECT * FROM `fleets` 
			WHERE `current_x_coord` = '". $x ."' AND 
				`current_y_coord` = '". $y ."' AND
				`traveling` = 0
				". ($user_id ? " AND `owner` = '$user_id'" : "" ) ."
				");
		while ( $fleet_row = $qry->fetch_assoc() )
			$fleets[] = new Fleet($fleet_row);
		
		return $fleets;
	}
	
	// This function computes and saves many of the stats and pre-calculations
	// that this fleet will need for battle calculations.
	public function battle_prep()
	{
		$this->get_ships();
		
		// Make some ship objects for stat references.
		$ref_ships = Ship::get_reference_ships();

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
	
	// Calculate the new fleet speed and set it.
	public function calculate_speed()
	{
		$this->get_ships();
		$ref_ships = Ship::get_reference_ships();
		$fleet_speed = 99999999;
		foreach ( $this->ships as $type => $fship )
		{
			if ( $ref_ships[$type]->speed < $fleet_speed )
				$fleet_speed = $ref_ships[$type]->speed;
		}
		$this->speed = $fleet_speed;
	}
	
	// This function gets called when this fleet has reached its destination.
	public function destination_reached()
	{
		// Is this fleet reaching its target or its home?
		if ( $this->home_x_coord == $this->to_x_coord &&
				 $this->home_y_coord == $this->to_y_coord  )
		{
			// This fleet just arrived back home.
			$this->current_x_coord = $this->home_x_coord;
			$this->current_y_coord = $this->home_y_coord;
			$this->traveling = 0;
		}
		else
		{
			// Perform the action for this fleet's mission(s).
			if ( Fleet::$objectives1[$this->primary_objective] == 'attack' )
			{
				$mf1 = new Mega_Fleet([$this]);
				// TODO: retrieve the defending fleets.
				$away_fleets = Fleet::fleets_at($this->to_x_coord, $this->to_y_coord);
				$participating_away_fleets = array();
				foreach ( $away_fleets as $away_fleet )
				{
					$missn = Fleet::$objectives2[$away_fleet->primary_objective];
					if ( $missn == 'defend_object' || $missn == 'attack_visitors'
							|| $missn == 'attack_if_attacked' )
					{
						$participating_away_fleets[] = $away_fleet;
					}
				}
				$mf2 = new Mega_Fleet($participating_away_fleets);
				Mega_Fleet::fleet_battle($mf1, $mf2);
			}
			
			// Send the fleet back home.
			$this->speed = $this->calculate_speed();
			$this->from_x_coord = $this->to_x_coord;
			$this->from_y_coord = $this->to_y_coord;
			$this->to_x_coord = $this->home_x_coord;
			$this->to_y_coord = $this->home_y_coord;
			$this->departure_time = $this->arrival_time;
			
			
			// TODO: possible complication: a fleet is out attacking but is scheduled to return
			// before an attack hits that fleet's home but the job queue doesn't contain the
			// fleet's return job until after its attack job is processed.
			// Cron?
			// alternative: enter both the going and returning jobs at the same time.
		}
		
		$this->save_data();
	}
}

?>