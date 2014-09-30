<?php

	$new_msg_qry = $Mysql->query("SELECT * FROM `messages` WHERE `to_player` ='".$User->id ."' ORDER BY `time` DESC"); 

	// Keep track of which player IDs need to be looked up to find their usernames.
	$player_ids = array();
	
	$this->data['messages'] = array();
	while ( $message_row = $new_msg_qry->fetch_assoc()){
		$this->data['messages'][] = $message_row;
		
		// Add this player's ID to the list of IDs to lookup.
		if ( !in_array($message_row['from_player'], $player_ids) )
			$player_ids[] = $message_row['from_player'];
	}
	
	$new_msg_qry = $Mysql->query("SELECT * FROM `messages` WHERE `from_player` ='".$User->id ."' ORDER BY `time` DESC"); 
	
	$this->data['messages_sent'] = array();
	while ( $message_row = $new_msg_qry->fetch_assoc()){
		$this->data['messages_sent'][] = $message_row;
		
		// Add this player's ID to the list of IDs to lookup.
		if ( !in_array($message_row['to_player'], $player_ids) )
			$player_ids[] = $message_row['to_player'];
	}
	
	// Lookup usernames for each of the relevant player ID.
	$this->data['usernames'] = array();
	if ( !empty($player_ids) )
	{
		$qry_str_part1 = "SELECT `id`, `username` FROM `players` WHERE ";
		$qry_str_part2 = "";
		foreach ( $player_ids as $a_player_id )
			$qry_str_part2 .= "`id` = '". $a_player_id ."' OR ";
		$qry_str_part2 = substr($qry_str_part2, 0, -3);
		$usernames_qry = $Mysql->query($qry_str_part1 . $qry_str_part2);
		//echon($qry_str_part1 . $qry_str_part2);
		while ( $user_row = $usernames_qry->fetch_assoc() )
		{
			$this->data['usernames'][$user_row['id']] = $user_row['username'];
		}
	}
	
?>