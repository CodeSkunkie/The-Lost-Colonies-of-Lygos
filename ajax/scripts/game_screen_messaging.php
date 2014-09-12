<?php

	$new_msg_qry = $Mysql->query("SELECT * FROM `messages` WHERE `to_player` ='".$User->id ."'"); 
	
	$this->data['messages'] = array();
	while ( $message_row = $New_msg_qry->fetch_assoc()){
		$this->data['messages'][] = $message_row;
	}
	

?>