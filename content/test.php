<?php

$this->require_login();

echo (clean_text("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ".
		"0123456789!@#$%^&*(){}[]-_"));

//session_destroy();


?>