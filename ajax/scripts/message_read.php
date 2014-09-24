<?php

	//Fetching Values from URL
	$viewed2=$_GET['viewed1'];
	$id2=$_GET['id1'];
	//Insert query
	$form_query = $Mysql->query("UPDATE messages 
		SET `viewed`='$viewed2'
		WHERE `id`='$id2'");
		
	
?>