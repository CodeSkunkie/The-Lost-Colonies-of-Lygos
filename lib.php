<?php

function console_log($string)
{
	echo '<script type="text/javascript">console.log("'. $string .'");</script>';
}

function load_class($name)
{
	if ( !class_exists($name) )
		require(WEBROOT .'classes/'. $name .'.php');
}

?>