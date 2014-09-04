<?php

// This script deletes old entries in the failed_logins table.

$Mysql = new mysqli("127.0.0.1", "lygos", "3o_e}.890h.23._lm", "lygos");
if ($Mysql->connect_errno)
    exit('Could not connect to Lygos database.');

$Mysql->query("DELETE FROM `failed_logins` 
	WHERE `time` < ". (time() - (24*60*60)) ." ");


?>