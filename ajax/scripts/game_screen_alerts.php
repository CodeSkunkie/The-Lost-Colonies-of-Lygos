<?php

	$new_msg_qry = $Mysql->query("SELECT * FROM `alerts` WHERE `player_id` ='".$User->id ."' ORDER BY `timestamp` DESC"); 

	// Keep track of which player IDs need to be looked up to find their usernames.
	$player_ids = array();
	
	$this->data['alerts'] = array();
	while ( $message_row = $new_msg_qry->fetch_assoc()){
		$this->data['alerts'][] = $message_row;
		
	}
	
?>