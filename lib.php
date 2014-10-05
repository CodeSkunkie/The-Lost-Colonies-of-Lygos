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

// Return the distance between to hexagonal tiles
// Note: at this time, the database and map call the z coordinate y.
//		This function should be given the "x and y" coordinates from
//		the database.
function hex_distance($x1, $z1, $x2, $z2)
{
	// Convert to cube-coordinates first.
	// Derive the missing coordinate.
    $y1 = -$x1 - $z1;
    $y2 = -$x2 - $z2;
    return (abs($x1 - $x2) + abs($y1 - $y2) + abs($z1 - $z2)) / 2;
}

?>