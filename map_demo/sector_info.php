<?php

$x = clean_number($_GET['x']);
$y = clean_number($_GET['y']);
$z = clean_number($_GET['z']);

$obj_query = mysql_query("SELECT * FROM `map`
	WHERE `x` = $x AND `y` = $y AND `z` = $z", $db_link);
$row = mysql_fetch_assoc($obj_query);
$obj = $row['object'];
$obj_id = $row['obj_id'];

if ( $obj == 'station' )
{
	$info_query = mysql_query("SELECT * FROM `stations`, `users`
		WHERE `stations`.`id` = $obj_id AND
			`users`.`id` = `stations`.`user_id`", $db_link);
	$obj_info = mysql_fetch_assoc($info_query);
	echo ''. $obj_info['username'] .'&#39;s space station ('. $x .', '. $y .', '. $z .')';
}
else if ( $obj == 'asteroid' )
{
	echo 'asteroid ('. $x .', '. $y .', '. $z .')';
}
else if ( $obj == '' )
{
	echo 'empty space';
}

echo '<br />last scanned: never';



?>