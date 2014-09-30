

// This function is called when someone clicks the map hologram. brian
$('#link_div_map').click(function() {
	// Grab the name of this screen
	var name = $(this).attr('id').substr(9);
	// Call the data-fetching script for this screen.
	request_data('game_screen_' + name, {"coord_x": center_tile_x, "coord_y": center_tile_y}, function(json_data) {
		// If data was successfully fetched...
		if ( json_data.ERROR == "" )
		{
			// Erase any old contents on this screen.
			$('#map_container_div').html('');
			$('#map_tile_selector_div').html('');

			// Populate the content of this screen.
			draw_map(json_data.tiles, name);
			// REFERENCE: figure out how to _actually_ iterate over the axial coordinate system.
			//		      go here and search 'axial coordinates':
			//		      http://www.redblobgames.com/grids/hexagons/
		}
		else
			alert(json_data.ERROR);
	});
});


function draw_map(tiles, name) {
	// center_tile_x, center_tile_y are x,y of player home base tile (in database?)
	// screen x,y offsets for top left of map area
	var div_y_offset=100;
	var div_x_offset=260;
	// used to number tiles with coordinates
	var rel_x=0;
	var rel_y=-2;

	// 22 map tiles are shown at once, center tile is 12th tile (i=11)
	// 1st tile is (center-0,center-2)
	// adds map tile divs to map
	for (var i=0; i<23; i++)
	{
		// sets up tile offsets and coordinates
		if (i==4) {div_y_offset-=93; div_x_offset-=330; rel_x=-1; rel_y++;}
		else if (i==7) {div_y_offset-=61; div_x_offset+=588;}
		else if (i==9) {div_y_offset-=124; div_x_offset-=396; rel_x=-2; rel_y++;}
		else if (i==14) {div_y_offset-=184; div_x_offset+=192; rel_x=-3; rel_y++;}
		else if (i==19) {div_y_offset-=93; div_x_offset-=330; rel_x=-3; rel_y++;}
		else if (i==21) {div_y_offset-=61; div_x_offset+=378;}

		// choose tile image file based on database info
		var image = 'tile_empty.png';
		var tile = tiles[center_tile_x+rel_x];
		tile = ((typeof(tile) == "undefined") ? tile : tile[center_tile_y+rel_y]);
		if (typeof(tile) != "undefined") {
			if (tile.player_has_vision == "1") image = 'tile_current.png';
			else if (tile.player_has_vision == "0" && tile.cache != "") image = 'tile_outdated.png';
		}

		// actually create divs with proper tile image
		var div=jQuery('<img>', {
			"id": 'map_tile'+ i + 'div',
			"class": 'map_tile_div',
			"src": 'media/themes/default/images/'+image,
		});

		// create hilighting divs
		var selector=jQuery('<img>', {
			"id": 'map_tile_selector'+ i + 'div',
			"class": 'map_tile_div_select_off',
			"src": 'media/themes/default/images/tile_selected.png',
			"title": '(' + (center_tile_x+rel_x) + ',' + (center_tile_y+rel_y) + ')',
			"x": (center_tile_x+rel_x),
			"y": (center_tile_y+rel_y)
		});

		// set position of divs
		div.offset({top:div_y_offset,left:div_x_offset});
		selector.offset({top:div_y_offset,left:div_x_offset});

		// adds tile divs to map div
		div.appendTo('#map_container_div');
		selector.appendTo('#map_tile_selector_div');

		// move tile offsets down and to the right
		div_x_offset-=18;
		div_y_offset+=31;
		rel_x++;

		// highlight map tile on hover
		selector.on('mouseover mouseout', function() {
			$(this).toggleClass('map_tile_div_select_on');
			$(this).toggleClass('map_tile_div_select_off');
		});
	}
	// Display this screen.
	change_screen(name);
}

