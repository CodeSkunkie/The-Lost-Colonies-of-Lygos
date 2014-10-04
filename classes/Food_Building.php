<?php

class Food_Building extends Colony_Building
{
	// Fields taken directly from the database:
	public $id, $colony_id, $type =2, $level;
	
	// Extra fields:
	public $name = 'Meat Lands';
	public $long_descript = 'AKA: "Space-Pasture". Food is grown here to feed everyone in this colony.';
	protected $db_table_name = 'buildings';
	protected $extra_fields = array('db_table_name', 'name', 'long_descript', 'extra_fields', 'building_object');
	
	public function upgrade_cost()
	{
		return new Resource_Bundle(
			10 * $this->level + 15,
			10 * $this->level + 35,
			10 * $this->level + 20,
			10 * $this->level + 20);
	}
	
	protected function resource_production($level)
	{
		if ($level == 0)
			return new Resource_Bundle(0,0,0,0);
		
		return new Resource_Bundle( (pow($level,2) * 0.2) + ($level * 2.5),	0,0,0);
	}
	
	public function current_resource_production()
	{
		return $this->resource_production($this->level);
	}
	
	public function next_resource_production()
	{
		return $this->resource_production($this->level +1);
	}
	
	// This function gets called whenever this building gets upgraded.
	public function finish_upgrade($colony)
	{
		// Add the new resource production for this upgrade.
		$old_gain_rate = $this->current_resource_production();
		$new_gain_rate = $this->next_resource_production();
		foreach ( $new_gain_rate as $res_name => $value )
		{
			$colony->resources->$res_name->production_rate += 
				($new_gain_rate->$res_name - $old_gain_rate->$res_name);
		}
		
		// Increase the level of this building.
		$this->level++;
	}
	
	public function upgrade_duration()
	{
		return 20;
	}
}

?>
