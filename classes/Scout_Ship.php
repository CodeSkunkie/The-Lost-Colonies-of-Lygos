<?php

class Scout_Ship extends Ship
{
	
	public $name = 'Scout Ship';
	public $long_descript = 'It is quick and great for exploring, but this ship should avoid battle and does not have much space for cargo.';
	public $type = 1;
	public $attack = 2;
	public $defense = 8;
	public $hp = 50;
	public $shield = 5;
	public $capacity = 1;
	public $cargo;
	public $speed = 1500;
	public $level;
	public $accuracy = 70;
	public $evasion = 90;
	
	public function build_duration()
	{
		
	}
	
	// This function gets called whenever this ship gets built.
	public function finish_build($colony)
	{
		
	}
}

?>
