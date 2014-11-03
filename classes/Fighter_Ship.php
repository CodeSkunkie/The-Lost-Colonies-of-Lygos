<?php

class Fighter_Ship extends Ship
{
	
	public $name = 'Fighter Ship';
	public $long_descript = 'It is quick and built for attacking, but this ship will not last long when fired on and cannot transport much anything.';
	public $type = 0;
	public $attack = 10;
	public $defense = 1;
	public $hp = 50;
	public $shield = 3;
	public $capacity = 1;
	public $cargo;
	public $speed = 1500;
	public $level;
	public $accuracy = 70;
	public $evasion = 70;
	
	public function upgrade_duration()
	{
		
	}
	
	// This function gets called whenever this ship gets built.
	public function finish_build($colony)
	{
		
	}
}

?>
