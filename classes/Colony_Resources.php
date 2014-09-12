<?php

class Colony_Resources
{
	public $food, $water, $metal, $energy;
	// Resource rates are stored in the form of units per hour.
	
	function __construct($colony_row)
	{
		$i=1;
		foreach ( $this as $field => $values )
		{
			$this->$field = new Colony_Resource(
				$colony_row['resource'. $i . '_capacity'], 
				$colony_row['resource'. $i . '_stock'],
				$colony_row['resource'. $i . '_production_rate'],
				$colony_row['resource'. $i . '_consumption_rate']);
			$i++;
		}
		//$this->colony_id = $colony_row['id'];
	}
	
	public function update($hours_passed)
	{
		// Calculate the new resource quantities and update this object accordingly.
		foreach ( $this as $field => $value )
		{
			$this->$field->update($hours_passed);
		}
	}
	
	public function print_summary()
	{
		foreach ( $this as $field => $resource )
		{
			echo '<image src="media/themes/default/images/leaf.png" /> '. round($resource->stock) .' / '. $resource->capacity .'<br />';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if ( $resource->consumption_rate == 0 )
				echo '+'. $resource->production_rate .' per hour';
			else
				echo '+'. $resource->production_rate .' -'. $resource->consumption_rate .' = '. ($resource->production_rate - $resource->consumption_rate);
			br();
		}
	}
}

?>