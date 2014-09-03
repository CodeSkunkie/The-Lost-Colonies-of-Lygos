<?php

// This file is currently not being used.

$core_classes = array('User', 'Page', 'Colony');

foreach ( $core_classes as $class )
	require(WEBROOT .'classes/'. $class .'.php');


?>