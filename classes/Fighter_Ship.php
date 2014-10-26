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
	public $speed = 10;
	public $level;
	public $accuracy = 0.7;
	public $evasion = 0.7;
	
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
