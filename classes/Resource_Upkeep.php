<?php

// This class defines a rate of resource consumption in a convenient standardized package format.
class Resource_Upkeep
{
	public $food, $water, $metal, $energy;
	
	function __construct($food, $water, $metal, $energy)
	{
		foreach ( $this as $field => $amount )
		{
			$this->$field = $amount;
		}
	}
}

?>