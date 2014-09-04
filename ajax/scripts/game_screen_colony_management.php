<?php
	require(WEBROOT .'classes/Colony.php');
	
	$this->data['colony'] = new Colony($User->colony_ids[0]);
	
	// TODO: handle the case where the user's login session has timed out.
?>