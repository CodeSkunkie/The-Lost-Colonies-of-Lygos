<?php
	// Include the class definition for map tiles.
	require(WEBROOT .'classes/World_Object.php');
	
	// Build up this array for returning to the caller-page/script.
	$tiles = array();
	
	// Which tile is in the center of the current view?
	$center_x = clean_text($_GET['coord_x']);
	$center_y = clean_text($_GET['coord_y']);
	
	// Fetch tile data for all tiles within a certain radius.
	$tile_radius = 4;
	$max_x = $center_x + $tile_radius;
	$min_x = $center_x - $tile_radius;
	$max_y = $center_y + $tile_radius;
	$min_y = $center_y - $tile_radius;
	
	// TODO: improve this selector to grab only the necessary tiles.
	$cache_qry = $Mysql->query("SELECT * FROM `player_tiles_cache` WHERE
		`player_id` = '". $User->id ."' AND
		`x_coord` <= '". $max_x ."' AND
		`x_coord` >= '". $min_x ."' AND
		`y_coord` <= '". $max_y ."' AND
		`y_coord` >= '". $min_y ."'");
	
	$tiles = array();
	while ( $tile_cache_row = $cache_qry->fetch_assoc() )
	{
		$tile_data = array();
		foreach( $tile_cache_row as $field => $val )
			$tile_data[$field] = $val;
		if ( !isset($tile_data['x_coord']) )
			$tile_data['x_coord'] = array();
		$tiles_data[$tile_data['x_coord']][$tile_data['y_coord']] = $tile_data;
	}
	$this->data['tiles'] = $tiles_data;
	
?>