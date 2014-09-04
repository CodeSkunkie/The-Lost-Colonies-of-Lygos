<?php

class Colony_Resource
{
	public $stock, $capacity, $production_rate, $consumption_rate;
	
	function __construct($capacity, $stock, $production_rate, $consumption_rate)
	{
		$this->capacity = $capacity;
		$this->stock = $stock;
		$this->production_rate = $production_rate;
		$this->consumption_rate = $consumption_rate;
	}
	
	public function update($hours_passed)
	{
		$net_prod_rate = $this->production_rate - $this->consumption_rate;
		$this->stock += ( $hours_passed * $net_prod_rate);
		if ( $this->stock > $this->capacity )
			$this->stock = $this->capacity;
	}
}

?>