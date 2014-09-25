<?php

	//Fetching Values from URL
	$from2=$User->id;	
	$to2=clean_text($_GET['to1']);
	$message2=clean_text($_GET['message1']);
	$subject2=clean_text($_GET['subject1']);
	$time2=time();
	
	// Lookup the id of the player whose username was specified.
	$username_qry = $Mysql->query("SELECT `id` FROM `players` 
		WHERE lower(`username`) = '". strtolower($to2) ."'");
	if ( $username_qry->num_rows == 0 )
		return_warning('unknown_username');
	else
	{
		$player_row = $username_qry->fetch_assoc();
		$to_player_id = $player_row['id'];
		
		//Insert query
		$form_query = $Mysql->query("INSERT INTO messages 
			SET `from_player`='$from2', 
				`to_player`='$to_player_id', 
				`message`='$message2',
				`subject`='$subject2',
				`viewed`=0,
				`time`='$time2'");
	}
	
?>