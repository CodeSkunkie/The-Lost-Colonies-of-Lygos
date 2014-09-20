<?php

	$new_msg_qry = $Mysql->query("SELECT * FROM `messages` WHERE `to_player` ='".$User->id ."'"); 
	
	$this->data['messages'] = array();
	while ( $message_row = $new_msg_qry->fetch_assoc()){
		$this->data['messages'][] = $message_row;
	}
	
	$new_msg_qry = $Mysql->query("SELECT * FROM `messages` WHERE `from_player` ='".$User->id ."'"); 
	
	$this->data['messages_sent'] = array();
	while ( $message_row = $new_msg_qry->fetch_assoc()){
		$this->data['messages_sent'][] = $message_row;
	}
	

?>