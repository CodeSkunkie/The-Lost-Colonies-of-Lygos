<?php

class Fighter_Research extends Research_Item
{
	public $id, $colony_id, $type = 1, $level;
	public $name = 'Fighter Ship Upgrade';
	public $long_descript = 'It is quick and built for attacking, but this ship will not last long when fired on and cannot transport much anything.';

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
