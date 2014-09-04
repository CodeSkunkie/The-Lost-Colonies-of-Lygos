<?php

define('WEBROOT', '/var/www/');


//require('core_classes.php');
//require('functions.php');

// Data
$grid_width = 5; // All grid dimensions must be odd numbers.
$grid_height = 5;
$cube_depth = 5;
$map_colors = array('purple', 'blue', 'green', 'yellow', 'red');

// Get coords on which to center.
if ( isset($_GET['x']) && isset($_GET['y']) && isset($_GET['z']) )
{
	$centered_x = clean_number($_GET['x']);
	$centered_y = clean_number($_GET['y']);
	$centered_z = clean_number($_GET['z']);
}
else if ( isset($_SESSION['x']) && isset($_SESSION['y']) && isset($_SESSION['z']) )
{
	$centered_x = $_SESSION['x'];
	$centered_y = $_SESSION['y'];
	$centered_z = $_SESSION['z'];
}
else
{
	// Center on the user's primary station.
	$centered_x = 0;
	$centered_y = 0;
	$centered_z = 0;
}

echo '<h2>Map Room</h2>';
echo '
<span id="map_table">
loading map...
</span>';


?>

<script type="text/javascript">
	function map_reload(x, y, z)
	{
		$.get('?page_element=map_reload&x='+ x +'&y='+ y +'&z='+ z, function(data) {
			$("#map_table").html(data);
		});
	}
	function show_sector_info(x, y, z, obj, obj_id)
	{retprn;
		$.get('?page_element=sector_info&x='+ x +'&y='+ y +'&z='+ z +'&obj='+ obj +'&obj_id='+ obj_id, function(data) {
			$("#sector_info").html(data);
		});
	}
	function hide_sector_info()
	{return;
		$("#sector_info").html('[sector info]');
	}
	
	$(document).ready(function(){
		map_reload(<?php echo $centered_x .', '. $centered_y .', '. $centered_z; ?> );
	});
</script>