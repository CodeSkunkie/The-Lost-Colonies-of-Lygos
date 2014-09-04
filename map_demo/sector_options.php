<?php

$sector_x = clean_number($_GET['x']);
$sector_y = clean_number($_GET['y']);
$sector_z = clean_number($_GET['z']);

// Get data from the map for the viewable space.
$map_query = mysql_query("SELECT * FROM `map` 
	WHERE `x` = $sector_x AND
		`y` = $sector_y AND
		`z` = $sector_z ", $db_link);
if ( mysql_num_rows($map_query) > 0 )
{
	$row = mysql_fetch_assoc($map_query);
	$sector_object = $row['object'];
}
else
{
	$sector_object = 'none';
}


echo 'Sector object: '. $sector_object .' ('. $sector_x .', '. $sector_y .', '. $sector_z .')<br />
	Send attack<br />
	Send reinforcements<br />
	Send probe (ability to leave probe or do round-trip)<br />
	Send supplies<br />
	Add to favorite places<br />
	<a href="?content=map&x='. $sector_x .'&y='. $sector_y .'&z='. $sector_z .'">
		Center on map
	</a><br />';
	
?>