<?php

class Cargo_Ship extends Ship
{
	
	public $name = 'Cargo Ship';
	public $long_descript = 'It will take a bunch of stuff from point A to point B at a decent pace. This ship can take some hits, but it can\'t fight back with much force.';
	public $type = 3;
	public $attack = 1;
	public $defense = 10;
	public $hp = 50;
	public $shield = 5;
	public $capacity = 10;
	public $cargo;
	public $speed = 5;
	public $level;
	public $accuracy = 0.2;
	public $evasion = 0.05;
	
	public function build_duration()
	{
		return $this->level+1 * 30;
	}
	
	// This function gets called whenever this ship gets built.
	public function finish_build($colony)
	{
		
	}
}

?>
