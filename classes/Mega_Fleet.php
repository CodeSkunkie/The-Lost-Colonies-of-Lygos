<?php

class Mega_Fleet
{
	public $fleets, $stats, $ships;
	
	public function __construct($fleets)
	{
		$this->fleets = $fleets;
		
		$this->stats = [];
		$this->ships = [];
		foreach ( $this->fleets as $fleet )
		{
			$fleet->battle_prep();
			
			// Add ships from each fleet into the mega fleet.
			foreach ($fleet->ships as $fship)
			{
				// See if this type of ships is already in the ships array.
				if ( $megafship = $this->find_ship_type($fship->type) )
					$megafship->count += $fship->count;
				else
					$this->ships[] = $fship;
			}
		}
		
		$this->calculate_stats();
	} // End: constructor
	
	private function find_ship_type($type)
	{
		$found = false;
		foreach ( $this->ships as $fship )
		{
			if ( $fship->type = $type )
				$found = $fship;
		}
		return $found;
	}
	
	private function calculate_stats()
	{
		// List of stat names to tally.
		$stat_names = array('atk', 'def', 'shield', 'hp');
		
		// Make some ship objects for stat references.
		$ref_ships = array();
		foreach ( $i = 0; $i < count(Ship::$types); $i++ )
			$ref_ships[$i] = Ship::construct_child(['type' => $i]);
		
		foreach ( $this->ships as $fship )
		{
			// Get a reference to the generic ship object with ship stats for this type of ship.
			$ship = $ref_ships[$fship->type];

			$this->stats = array();
			foreach ( $stat_names as $stat_name )
				$this->stats[$stat_name] += ($ship->$stat_name * $fship->count);
		}
	}
	
	// Calculates a battle between two sets of opossing forces.
	// inputs: 
	//			$mf1: Mega_Fleet object.
	//			$mf2: Mega_Fleet object.
	public static function fleet_battle($mf1, $mf2)
	{
		// TODO: how to factor in the 'homefield advantage' and stationary defenses?
		
		// TODO: factor in each fleet's primary and secondary missions.
		
		// Calculate a "normal" battle where both fleets are attacking.
		// Remove ships from both sides based on attack differentials.
		$mf1->take_damage($mf2->stats['atk'] - $mf1->stats['def']);
		$mf2->take_damage($mf1->stats['atk'] - $mf2->stats['def']);
		
		
		// TODO: remove the fleet row if all ships were destroyed?
		
		// TODO: generate battle reports for players.
	}
	
	// This function distributes damage to ships within the megafleet.
	private function take_damage($dmg)
	{
		$dmg_to_distribute = $dmg;
		// Array to keep track of where the damage lands (distribution).
		$mf1_dmg_distr = array();
		// Iterate over each damage point.
		while ( $dmg_to_distribute > 0 )
		{
			$dmg_found_target = false;
			// Iterate through all the ship-types in the megafleet.
			foreach ( $this->ships as $fship )
			{
				// Iterate through each ship of this ship type.
				for ( $i = 0; $i < $fship->count )
				{
					
					// roll a d100 to see if the damage hits this target.
					$hit_roll = mt_rand(1,100);
					if ( $hit_roll > $ref_ships[$fship->type]->evasion )
					{
						$dmg_found_target = true;
						$mf1_dmg_distr[$fship->type]++;
						break;
					}
				}
				if ( $dmg_found_target )
					break;
			}
			$dmg_to_distribute--;
		}
		
		// Remove some ships from megafleet1 based on the damage distribution.
		foreach ( $mf1_dmg_distr as $ship_type => $damage )
		{
			$mf_type_deaths = $damage / $ref_ships[$ship_type]->hp;
			$this->ships[$ship_type] -= $mf_type_deaths;
			// Remove a proportional number of ships of this type from each fleet in mf1.
			foreach ( $this->fleets as $fleet )
			{
				// Proportion of this ship type that came from this fleet.
				$proportion = $fleet->ships[$ship_type]->count / $ref_ships[$ship_type]->count;
				$f_type_deaths = round($mf_type_deaths * $proportion);
				$fleet->ships[$ship_type] -= $f_type_deaths;
			}
		}
		
		// Save new ship counts to the database for each fleet in mf1.
		foreach ( $this->fleets as $fleet )
			$fleet->save_ships();
	}
}

?>