<?php

class Cargo_Research extends Research_Item
{
	public $id, $colony_id, $type = 3, $level;
	public $name = 'Fighter Ship Upgrade';
	public $long_descript = 'It will take a bunch of stuff from point A to point B at a decent pace. This ship can take some hits, but it can\'t fight back with much force.';

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
