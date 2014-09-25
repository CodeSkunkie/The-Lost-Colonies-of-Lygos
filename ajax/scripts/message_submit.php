<?php

	//Fetching Values from URL
	$from2=clean_text($_GET['from1']);	
	$to2=clean_text($_GET['to1']);
	$message2=clean_text($_GET['message1']);
	$subject2=clean_text($_GET['subject1']);
	$viewed2=clean_text($_GET['viewed1']);
	$time2=time();
	//Insert query
	$form_query = $Mysql->query("INSERT INTO messages 
		SET `from_player`='$from2', 
			`to_player`='$to2', 
			`message`='$message2',
			`subject`='$subject2',
			`viewed`='$viewed2',
			`time`='$time2'");
	
?>