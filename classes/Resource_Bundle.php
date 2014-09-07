<?php

// This class defines a specific amount of resources in a convenient standardized package format.
class Resource_Bundle
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