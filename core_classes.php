<?php

$core_classes = array('User', 'Page');

foreach ( $core_classes as $class )
	require(WEBROOT .'classes/'. $class .'.php');


?>