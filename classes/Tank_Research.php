<?php

class Tank_Research extends Research_Item
{
	public $id, $colony_id, $type = 2, $level;
	public $name = 'Fighter Ship Upgrade';
	public $long_descript = 'It is slow-moving but well-shielded and very powerful in battle. Don\'t expect it to carry much.';

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
