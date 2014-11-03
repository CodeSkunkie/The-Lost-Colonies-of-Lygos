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
	public $speed = 500;
	public $level;
	public $accuracy = 95;
	public $evasion = 7;
	
	
}

?>
