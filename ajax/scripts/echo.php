<?php
	$message = clean_text($_GET['message']);
	$this->data['message'] = '_'. $message .'_';
?>