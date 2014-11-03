<?php

class Scout_Research extends Research_Item
{
	public $id, $colony_id, $type = 0, $level;
	public $name = 'Scout Ship Upgrade';
	public $long_descript = 'It is quick and great for exploring, but this ship should avoid battle and does not have much space for cargo.';

	/*public function __construct($lvl) {
		$extra_fields = 
		$level = $lvl;
   	}*/

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
