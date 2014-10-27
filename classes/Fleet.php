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
	// $speed is in tiles per hour.
	
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
		
		if ( is_array($this->ships) )
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
		if ( is_array($this->ships) )
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
		$stat_names = array('attack', 'defense', 'shield', 'hp');
		
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
		global $Mysql;
		
		// Is this fleet reaching its target or its home?
		if ( $this->home_x_coord == $this->to_x_coord &&
				 $this->home_y_coord == $this->to_y_coord  )
		{
			// This fleet just arrived back home.
			// Merge its ships into the home fleet if one exists.
			$fleets_here = Fleet::fleets_at($this->home_x_coord, $this->home_y_coord, $this->owner);
			if ( !empty($fleets_here) )
			{
				$home_fleet = $fleets_here[0];
				$home_fleet->get_ships();
				$this->get_ships();
				foreach ( $this->ships as $type => $fship )
				{
					if ( isset($home_fleet->ships[$type]) )
						$home_fleet->ships[$type]->count += $fship->count;
					else
					{
						$home_fleet->ships[$type] = new Fleet_Ship([
							'type' => $fship->type,
							'count' => $fship->count]);
						
					}
				}
				$home_fleet->save_ships();
				$this->delete();
			}
			else
			{
				// No fleet at home. Make this the home fleet.
				$this->current_x_coord = $this->home_x_coord;
				$this->current_y_coord = $this->home_y_coord;
				$this->traveling = 0;
			}
		}
		else
		{
			// Perform the action for this fleet's mission(s).
			$mission = Fleet::$objectives1[$this->primary_objective];
			
			
			if ( $mission == 'hold_position' )
			{
				$this->current_x_coord = $this->to_x_coord;
				$this->current_y_coord = $this->to_y_coord;
				$this->traveling = 0;
			}
			else
			{
				if ( $mission == 'attack' )
				{
					$mf1 = new Mega_Fleet([$this]);
					// Retrieve the fleets that are already in the destination sector.
					$away_fleets = Fleet::fleets_at($this->to_x_coord, $this->to_y_coord);
					// See which away_fleets will be participating in the fight.
					$participating_away_fleets = array();
					foreach ( $away_fleets as $away_fleet )
					{
						$mission = Fleet::$objectives2[$away_fleet->primary_objective];
						if ( $mission == 'defend_object' || $mission == 'attack_visitors'
								|| $mission == 'attack_if_attacked' )
						{
							$participating_away_fleets[] = $away_fleet;
						}
					}
					$mf2 = new Mega_Fleet($participating_away_fleets);
					
					// FIGHT!
					Mega_Fleet::fleet_battle($mf1, $mf2);
				}
				else if ( $mission == 'scout' )
				{
					// Retrieve world objects, colonies in this sector.
					$a_colony = Colony::get_colony_at($this->to_x_coord, $this->to_y_coord);
					if ( $a_colony )
					{
						// TODO: finish writing this bit.
//						$Mysql->qry("INSERT INTO `player_tiles_cache`
//							SET ");
					}
				}
				
				// Reverse direction.
				
				// Calculate travel distance.
				$travel_distance = hex_distance(
						$this->from_x_coord, 
						$this->from_y_coord,
						$this->to_x_coord, $this->to_y_coord);
				// Calculate fleet speed.
				$this->calculate_speed();
				// Calculate travel duration.
				$travel_duration = $this->travel_time($travel_distance);
				
				// Send the fleet back home.
				$this->from_x_coord = $this->to_x_coord;
				$this->from_y_coord = $this->to_y_coord;
				$this->to_x_coord = $this->home_x_coord;
				$this->to_y_coord = $this->home_y_coord;
				$this->departure_time = $this->arrival_time;
				$this->arrival_time = $this->departure_time + $travel_duration;
				$this->primary_objective = 1;
				
				// Create a new job for the return trip.
				// See which colony's job queue to put this in.
				load_class('Colony');
				$colony = Colony::get_colony_at($this->home_x_coord, $this->home_y_coord);
				$Mysql->query("INSERT INTO `job_queue` SET
					`colony_id` = '". $colony->id ."',
					`type` = 3,
					`product_id` = '". $this->id ."',
					`product_type` = 1,
					`start_time` = ". $this->departure_time .",
					`duration` = '". $travel_duration ."',
					`completion_time` = '". $this->arrival_time ."'");
				
				// TODO: possible complication: a fleet is out attacking but is scheduled to return
				// before an attack hits that fleet's home but the job queue doesn't contain the
				// fleet's return job until after its attack job is processed.
				// Cron?
				// alternative: enter both the going and returning jobs at the same time.
				// Alternative: complete each job in order and as it gets completed, 
				//			re-query the job table to find the next job to execute.
			}
		}
		$this->save_data();
	}
	
	// Given a distance, returns a travel time in seconds.
	// $distance is the number of tiles moved between
	// along the chosen path.
	public function travel_time($distance)
	{
		// d = rt;		t = d/r;		r = d/t
		// $this->speed is stored in tiles per hour. 
		$hours = $distance / $this->speed;
		$seconds = $hours * 3600;
		return $seconds;
	}
	
	// Removes this fleet from the database.
	public function delete()
	{
		global $Mysql;
		
		// First, remove this fleet's ships from the DB.
		$this->get_ships();
		foreach ( $this->ships as $fship )
			$fship->delete();
		
		// Now delete the fleet.
		$Mysql->query("DELETE FROM `fleets` 
			WHERE `id` = '". $this->id ."' ");
	}
}

?>