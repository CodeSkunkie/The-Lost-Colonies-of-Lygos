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
	public $speed = 1000;
	public $level;
	public $accuracy = 20;
	public $evasion = 5;
	
	
}

?>
