<?php
	// Include the class definition for map tiles.
	require(WEBROOT .'classes/Map_Tile.php');
	require(WEBROOT .'classes/World_Object.php');
	
	// Build up this array for returning to the caller-page/script.
	$tile_data = array();
	
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
	$tiles_qry = $Mysql->query("SELECT * FROM `map_tiles` WHERE 
		`coord_x` <= '". $max_x ."' AND
		`coord_x` >= '". $min_x ."' AND
		`coord_y` <= '". $max_y ."' AND
		`coord_y` >= '". $min_y ."'");
	
	$tiles = array();
	$tile_cache_qry_pt2 = "";
	// Iterate through the tiles within the selected radius.
	while ( $tile_row = $tiles_qry->fetch_assoc() )
	{
		// Need to include non-generated tiles in this array?
		// Can't. This loop only iterates over generated tiles.
		$tiles[$tile_row['id']] = new Map_Tile($tile_row['id']);
		$tile_cache_qry_pt2 .= " `tile_id` = '". $tile_row['id'] ."' OR ";
	}
	$tile_cache_qry_pt2 = substr($tile_cache_qry_pt2, 0, -3);
	
	// Only return tile data that is known to this player
	$cache_qry = $Mysql->query("SELECT * FROM `player_tiles_cache`
		WHERE `player_id` = '". $User->id ."' ". 
			" AND ( ". $tile_cache_qry_pt2 ." ) ");
	$tiles_data = array();
	while ( $tile_cache_row = $cache_qry->fetch_assoc() )
	{
		$tile_data = array();
		$tile_data['id'] = $tile_cache['tile_id'];
		$tile_data['player_has_vision'] = $tile_cache['player_has_vision'];
		$tile_data['cache_time'] = $tile_cache['cache_time'];
		// $tile_data['cache'] = 
		$tile_data['coord_x'] = $tiles[$tile_cache['tile_id']]['coord_x'];
		$tile_data['coord_y'] = $tiles[$tile_cache['tile_id']]['coord_y'];
		$tiles_data[$tile_data['id']] = $tile_data;
	}
	$this->data['tiles_data'] = $tiles_data;
	
	
	
	//	// Which tile should be in the center of our map?
//	// This is given as input to this script either in
//	// the form of an ID or coordinates.
//	if ( isset($_GET['center_tile_id']) )
//	{
//		$center_tile = new Map_Tile(clean_text($_GET['center_tile_id']));
//	}
//	else
//	{
//		// This tile has not been generated yet.
//		$center_tile = new Map_Tile(clean_text($_GET['center_tile_x']), 
//			clean_text($_GET['center_tile_y']));
//	}
//	
?>