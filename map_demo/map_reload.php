<?php

// TODO: Make this page check through session vars if the user has probed the
// sector he wants to get info for.

// Data
$cube_width = 7; // All grid dimensions must be odd numbers.
$cube_height = 7;
$cube_depth = 7;
$map_colors = array('purple', '#4444ff', 'green', 'yellow', 'orange', 'red', 'white');

// Get coords on which to center. If not specified, center on own station.
if ( isset($_GET['x']) && isset($_GET['y']) && isset($_GET['z']) )
{
	$centered_x = clean_number($_GET['x']);
	$centered_y = clean_number($_GET['y']);
	$centered_z = clean_number($_GET['z']);
}
else
{
	$centered_x = 0;
	$centered_y = 0;
	$centered_z = 0;
}

// Save the centered coords in case the user clicks out of the map page and
// then clicks the back button to see the map again.
$_SESSION['centered_x'] = $centered_x;
$_SESSION['centered_y'] = $centered_y;
$_SESSION['centered_z'] = $centered_z;

$min_x = $centered_x - ( ($cube_width - 1) / 2 );
$max_x = $centered_x + ( ($cube_width - 1) / 2 );
$min_y = $centered_y - ( ($cube_height - 1) / 2 );
$max_y = $centered_y + ( ($cube_height - 1) / 2 );
$min_z = $centered_z - ( ($cube_depth - 1) / 2 );
$max_z = $centered_z + ( ($cube_depth - 1) / 2 );

// Get data from the map for the viewable space.
$map_query = mysql_query("SELECT * FROM `map` 
	WHERE `x` >= $min_x AND `x` <= $max_x AND
		`y` >= $min_y AND `y` <= $max_y AND
		`z` >= $min_z AND `z` <= $max_z ", $db_link);
$poi = array(); // points of interest
$num_rows = mysql_num_rows($map_query);
for ($i = 0; $i < $num_rows; $i++)
{
	$row = mysql_fetch_assoc($map_query);
	$poi[$row['x']][$row['y']][$row['z']] = $row['object'];
}


echo '
<table width="100%">
<tr>
	<td>';
	
//	// Draw 2D map
//	echo '<table id="map_2d">';
//	for ( $y = $max_y; $y >= $min_y; $y-- )
//	{
//		for ( $x = $min_x; $x <= $max_x; $x++ )
//		{
//			// Check to see if there is anything at this location.
//			$char = '&nbsp;';
//			if ( $poi[$x][$y][$centered_z] == 'station' )
//				$char = 'X';
//			else if ( $poi[$x][$y][$centered_z] == 'asteroid' )
//				$char = '8';
//				
//			if ( $x == $min_x )
//				echo '<tr>';
//			echo '<td><a onMouseOut="hide_sector_info()" 
//				onMouseOver="show_sector_info('. $x .', '. $y .', '. $centered_z .')"
//				href="?content=sector_options&x='. $x .'&y='. $y .'&z='. $centered_z .'">
//					<font color="green">'. $char .'</font>
//				</a></td>';
//			if ( $x == $max_x )
//				echo '</tr>';
//		}
//	} 
//	echo '</table>';
//	
//echo '
//		</td>
//		<td>';

// Draw 3D map.
echo '<div style="position: relative; float: right;">
	<img src="pix/clear.gif" width="776" height="538" />';
echo '<div style="position: absolute; top: 0px; right: 0px;">
	<img src="pix/map_cube_'. $cube_width .'x'. $cube_height .'x'. $cube_depth .'.gif" />';
for ( $z = $min_z; $z <= $max_z; $z++ )
{
	for ( $y = $max_y; $y >= $min_y; $y-- )
	{
		for ( $x = $min_x; $x <= $max_x; $x++ )
		{
			// Check to see if there is anything at this location.
			$char = '';
			if ( $poi[$x][$y][$z] == 'station' )
				$char = 'X';
			else if ( $poi[$x][$y][$z] == 'asteroid' )
				$char = '8';
			
			//if ( $poi[$x][$y][$z] != '' )
			{
				echo '<div style="position: absolute; '. 
					'top: '. ( (abs($y - $max_y)  * 50) + (abs($z - $min_z) * 16) + 32) .'; '. 
					'left: '. ( (abs($x - $min_x) * 50) + (abs($z - $min_z) * 22) + 92 ) .';">';
				echo '<a onMouseOut="hide_sector_info()" 
					onMouseOver="show_sector_info('. $x .', '. $y .', '. $z .')"
					>
					<font color="'. $map_colors[($z - $min_z)] .'">'. $char .'</font>';
					// href="?content=sector_options&x='. $x .'&y='. $y .'&z='. $z .'"
				echo '</div>';
			}
		}
	}
	// Print z indeces.
	echo '<div style="position: absolute; '. 
		'top: '. ( (abs($z - $min_z) * 14) + ($cube_height * 50 + 20)) .'; '. 
		'left: '. ( (abs($z - $min_z) * 19) + 53 ) .';">';
	echo '<a href="javascript:'.
			'map_reload('. $centered_x .', '. $centered_y .', '. $z .')">
			<font color="'. $map_colors[($z - $min_z)] .'" size=2>'. $z .'</font>
		</a>';
	echo '</div>';
}

// Print y indeces.
for ( $y = $max_y; $y >= $min_y; $y-- )
{
	echo '<div style="position: absolute; '. 
		'top: '. ( (abs($y - $max_y) * 50) + ($cube_height * 16 + 18) ) .'; '. 
		'left: '. ( ($cube_width * 50) + 210 ) .';">';
	echo '<a href="javascript:'.
			'map_reload('. $centered_x .', '. $y .', '. $centered_z .')">
			<font size=2>'. $y .'</font>
		</a>';
	echo '</div>';
}

// Print x indeces.
for ( $x = $min_x; $x <= $max_x; $x++ )
{
	echo '<div style="position: absolute; '. 
		'left: '. ( (abs($x - $min_x) * 50) + ($cube_width * 22) + 64 ) .'; '. 
		'top: '. ( ($cube_height * 50) + 120 ) .';">';
	echo '<a href="javascript:'.
			'map_reload('. $x .', '. $centered_y .', '. $centered_z .')">
			<font size=2>'. $x .'</font>
		</a>';
	echo '</div>';
}
echo '</div>'; // end of cube div.

// Navigation div.
echo '<div style="position: absolute; top: 70px; left: 0px">';
// Axis navigation.
echo '<img src="pix/clear.gif" height="7" /><br />
	navigation:<br /><img src="pix/clear.gif" height="7" /><br />
	<img src="pix/axis_nav.png" usemap="#axis_nav_map" border=0 />';
echo '<map name="axis_nav_map">
		<area shape="polygon" coords="158, 77, 189, 93, 158, 108" href="javascript:'.
			'map_reload('. ($centered_x + 1) .', '. $centered_y .', '. $centered_z .')" />
		<area shape="polygon" coords="0, 94, 28, 80, 28, 110" href="javascript:'.
			'map_reload('. ($centered_x - 1) .', '. $centered_y .', '. $centered_z .')" />
		<area shape="polygon" coords="73, 28, 89, 0, 104, 28" href="javascript:'.
			'map_reload('. $centered_x .', '. ($centered_y + 1) .', '. $centered_z .')">
		<area shape="polygon" coords="89, 182, 102, 153, 75, 153" href="javascript:'.
			'map_reload('. $centered_x .', '. ($centered_y - 1) .', '. $centered_z .')">
		<area shape="polygon" coords="126, 141, 159, 147, 144, 117" href="javascript:'.
			'map_reload('. $centered_x .', '. $centered_y .', '. ($centered_z + 1) .')">
		<area shape="polygon" coords="23, 46, 36, 73, 54, 53" href="javascript:'.
			'map_reload('. $centered_x .', '. $centered_y .', '. ($centered_z - 1) .')">
	</map>';
// "jump to" form.
echo '<br /><img src="pix/clear.gif" height="5" /><br />
<form method="GET" action="index.php">('.
	'<input type="text" size=3 maxlength=3 name="x" value="'. $centered_x .'" />, '.
	'<input type="text" size=3 maxlength=3 name="y" value="'. $centered_y .'" />, '.
	'<input type="text" size=3 maxlength=3 name="z" value="'. $centered_z .'" />) '.
	'<input type="hidden" name="content" value="map" />'. 
	'<input type="submit" value="jump" />'.
'</form>';
echo '	</div>';


echo '<div style="position: absolute; bottom: 0px; right: 3px; white-space: nowrap;">
		<span id="sector_info">[sector info]</span>
	</div>';

echo '</div>'; // end outer-div.


echo '</td>
</tr>
</table>';

?>