<?php

class Tank_Ship extends Ship
{
	
	public $name = 'Tank Ship';
	public $long_descript = 'It is slow-moving but well-shielded and very powerful in battle. Don\'t expect it to carry much.';
	public $type = 2;
	public $attack = 12;
	public $defense = 8;
	public $hp = 50;
	public $shield = 5;
	public $capacity = 2;
	public $cargo;
	public $speed = 3;
	public $level;
	public $accuracy = 0.95;
	public $evasion = 0.075;
	
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
