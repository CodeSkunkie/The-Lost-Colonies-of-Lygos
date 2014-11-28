var sectors_click_listener_enabled = false;

// This function is called when someone clicks the map hologram link. brian
$('#link_div_map').click(function() {
	// Grab the name of this screen
	var name = $(this).attr('id').substr(9);

	// Call the data-fetching script for this screen.
	request_data('game_screen_' + name, {"coord_x": center_tile_x, "coord_y": center_tile_y}, function(json_data) {
		// Data was successfully fetched

		// Erase any old contents on this screen.
		$('#map_container_div').html('');
		$('#map_tile_selector_div').html('');

		// Populate the content of this screen.
		draw_map(json_data.tiles, name);
		// REFERENCE: figure out how to _actually_ iterate over the axial coordinate system.
		//		      go here and search 'axial coordinates':
		//		      http://www.redblobgames.com/grids/hexagons/
	});

	// clear nav panel
	$('#navigation_panel_div').html('');
	// Setup navigation panel for map (#navigation_panel_div)
	$('<span>map navigator</span>').appendTo('#navigation_panel_div');
	var nav_panel = jQuery('<img>', {
		"id": 'nav_panel_img_div',
		"class": 'nav_panel_base',
		"src": 'media/themes/default/images/nav_base.png',
		"usemap": '#nav_panel_map'
	});
	nav_panel.appendTo('#navigation_panel_div');
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

		// container div for base image and coordinate span
		var div=jQuery('<div>', {
			"class": 'map_tile_div'
		});

		// actually create image divs with proper tile image
		var base=jQuery('<img>', {
			"id": 'map_tile'+ i + 'div',
			"src": 'media/themes/default/images/'+image
		});

		// add coordinate display to tiles
		var span=jQuery('<span/>', {
			"id": 'map_tile'+i+'coords',
			"class": 'sector_coords'
		});
		span.text('('+(center_tile_x+rel_x)+','+(center_tile_y+rel_y)+')');

		// fill out tile container
		base.appendTo(div);
		span.appendTo(div);

		// create hilighting divs
		var selector = $('<img>', {
			"id": 'map_tile_selector'+ i + 'div',
			"class": 'map_tile_div_select',
			"src": 'media/themes/default/images/tile_selected.png',
			"x": center_tile_x+rel_x,
			"y": center_tile_y+rel_y
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
	}
	
	// This event listener must be made anew each time the
	// elemnts it's listening to get re-created.
	$('.map_tile_div_select').click(function(event) {
		$('#sector_menu').css('left', $(this).position().left +'px');
		$('#sector_menu').css('top', $(this).position().top +'px');
		$('#sector_menu').show(100);
		
		// Get the x and y coordinates for the sector the user clicked.
		var sector_x = $(this).attr('x');
		var sector_y = $(this).attr('y');
		var home_fleet = null;
		var ref_ships = [];
		
		// Retrieve the most up-to-date home-fleet info.
		request_data('get_fleet', 
			{"x": home_tile_x, "y": home_tile_y, "colony_id": colony_id},
			function(json_data) {
				home_fleet = json_data['fleet'];
				ref_ships = json_data['ref_ships'];
				
				// Populate the ship-selector menus.
				// Clear the ship selectors
				$('.ship_selector').html('');
				$('.dispatch_fleet_btn').show();
				// See if there is even a fleet at home.
				if ( home_fleet !== false )
				{
					var sector_actions = ['scouting', 'attack', 'holdpos', 'harvest'];
					for ( var i in sector_actions )
					{
						var saction = sector_actions[i];
						for ( var ship_type in home_fleet.ships )
						{
							$('<div/>', {
								"id": saction +"_ship_selector_ship"+ ship_type,
								"style": "float:left; margin-right: 7px;"
							}).appendTo('#'+ saction +'_ship_selector');
							$('<div/>', {
								"text": ref_ships[ship_type].name
							}).appendTo('#'+ saction +'_ship_selector_ship'+ ship_type);
							$('<img/>', {
								"src": "media/themes/default/images/ship"+ ship_type +".png",
								"style": "",
								"height": "100px"
							}).appendTo('#'+ saction +'_ship_selector_ship'+ ship_type);
							$('<br/>').appendTo('#'+ saction +'_ship_selector_ship'+ ship_type);
							$('<input/>', {
								"type": "number",
								"value": "0",
								"min": "0",
								"max": home_fleet.ships[ship_type].count,
								"style": "width:40px;",
								"id": saction +"_ship"+ ship_type +"_count"
							}).appendTo('#'+ saction +'_ship_selector_ship'+ ship_type);
							$('<span/>', {
								"text": " / "+ home_fleet.ships[ship_type].count
							}).appendTo('#'+ saction +'_ship_selector_ship'+ ship_type);
						}
						$('<div/>', {
							"style": "clear: left;"
						}).appendTo('#'+ saction +'_ship_selector');
					}
				}
				
				if ( !home_fleet || home_fleet.ships.length == 0 )
				{
					// No ships to select from.
					$('<div/>', {
						"text": "You have no ships at home!"
					}).appendTo('.ship_selector');
					
					$('.dispatch_fleet_btn').hide();
				}
			}
		);
		
		$('#dispatch_scouts_button').unbind('click');
		$('#dispatch_scouts_button').click(function() {
			var request_parameters = {"fleet_id": home_fleet_id, 
				"to_x_coord": sector_x,
				"to_y_coord": sector_y,
				"primary_objective": 2,
				"secondary_objective": 0,
				"from_colony_id": colony_id};
			for ( var i = 0; i < 4; i++  )
				request_parameters['ship'+ i +'_count'] = $('#scouting_ship'+ i +'_count').val();
			request_data('dispatch_fleet', 
				request_parameters,
				function(json_data) {
					if ( typeof json_data.WARNING != 'undefined' )
						alert('Warning: '+ json_data.WARNING);
					else
					{
						$('.dispatch_fleet_btn').hide();
						$('#scouting_popup').hide();
						$('.ship_selector').html('');
						fetch_jobs_queue();
					}
				}
			);
		});
		$('#dispatch_attack_button').unbind('click');
		$('#dispatch_attack_button').click(function() {
			var request_parameters = {"fleet_id": home_fleet_id, 
				"to_x_coord": sector_x,
				"to_y_coord": sector_y,
				"primary_objective": 0,
				"secondary_objective": 0,
				"from_colony_id": colony_id};
			for ( var i = 0; i < 4; i++  )
				request_parameters['ship'+ i +'_count'] = $('#attack_ship'+ i +'_count').val();
			request_data('dispatch_fleet', 
				request_parameters,
				function(json_data) {
					if ( typeof json_data.WARNING != 'undefined' )
						alert('Warning: '+ json_data.WARNING);
					else
					{
						$('.dispatch_fleet_btn').hide();
						$('#attack_popup').hide();
						$('.ship_selector').html('');
						fetch_jobs_queue();
					}
				}
			);
		});
		$('#dispatch_holdpos_button').unbind('click');
		$('#dispatch_holdpos_button').click(function() {
			var request_parameters = {"fleet_id": home_fleet_id, 
				"to_x_coord": sector_x,
				"to_y_coord": sector_y,
				"primary_objective": 1,
				"secondary_objective": 0,
				"from_colony_id": colony_id};
			for ( var i = 0; i < 4; i++  )
				request_parameters['ship'+ i +'_count'] = $('#holdpos_ship'+ i +'_count').val();
			request_data('dispatch_fleet', 
				request_parameters,
				function(json_data) {
					if ( typeof json_data.WARNING != 'undefined' )
						alert('Warning: '+ json_data.WARNING);
					else
					{
						$('.dispatch_fleet_btn').hide();
						$('#holdpos_popup').hide();
						$('.ship_selector').html('');
						fetch_jobs_queue();
					}
				}
			);
		});
		$('#dispatch_harvest_button').unbind('click');
		$('#dispatch_harvest_button').click(function() {
			var request_parameters = {"fleet_id": home_fleet_id, 
				"to_x_coord": sector_x,
				"to_y_coord": sector_y,
				"primary_objective": 4,
				"secondary_objective": 0,
				"from_colony_id": colony_id};
			for ( var i = 0; i < 4; i++  )
				request_parameters['ship'+ i +'_count'] = $('#harvest_ship'+ i +'_count').val();
			request_data('dispatch_fleet', 
				request_parameters,
				function(json_data) {
					if ( typeof json_data.WARNING != 'undefined' )
						alert('Warning: '+ json_data.WARNING);
					else
					{
						$('.dispatch_fleet_btn').hide();
						$('#harvest_popup').hide();
						$('.ship_selector').html('');
						fetch_jobs_queue();
					}
				}
			);
		});
	});
	sectors_click_listener_enabled = true;
	
	// Display this screen.
	change_screen(name);
}

// This function is called when someone hovers over the navigation panel
$('.map_nav_element').mouseover(function() {
	var button = $(this).attr('id');
	$('#nav_panel_img_div').attr('src', 'media/themes/default/images/'+button+'.png');
});

// This function is called when someone stops hovering over the navigation panel
$('.map_nav_element').mouseout(function() {
	$('#nav_panel_img_div').attr('src', 'media/themes/default/images/nav_base.png');
});

$('#sector_menu').mouseleave(function() {
	$(this).hide();
});

// This function is called when someone clicks on the navigation panel
$('.map_nav_element').click(function() {
	var button = $(this).attr('id');
	switch(button) {
		case 'nav_negY':
			center_tile_y--;
			break;
		case 'nav_negX':
			center_tile_x--;
			break;
		case 'nav_negZ':
			center_tile_x--;
			center_tile_y++;
			break;
		case 'nav_posY':
			center_tile_y++;
			break;
		case 'nav_posX':
			center_tile_x++;
			break;
		case 'nav_posZ':
			center_tile_x++;
			center_tile_y--;
			break;
		case 'nav_home':
			center_tile_x=home_tile_x;
			center_tile_y=home_tile_y;
			break;
		default:
			break;
	}

	refresh_map('map');
});

// pulls fresh map data from the database and populates to map screen
function refresh_map(name) {
	// Call the data-fetching script for this screen.
	request_data('game_screen_' + name, {"coord_x": center_tile_x, "coord_y": center_tile_y}, function(json_data) {
		// If data was successfully fetched...
		if ( json_data.ERROR == "" )
		{
			// new database info on map
			var tiles = json_data.tiles;

			// used to number tiles with coordinates
			var rel_x=0;
			var rel_y=-2;

			for (var i=0; i<23; i++) {
				// sets up tile coordinates
				if (i==4) {rel_x=-1; rel_y++;}
				else if (i==9) {rel_x=-2; rel_y++;}
				else if (i==14) {rel_x=-3; rel_y++;}
				else if (i==19) {rel_x=-3; rel_y++;}

				// choose tile image file based on database info
				var image = 'tile_empty.png';
				var tile = tiles[center_tile_x+rel_x];
				tile = ((typeof(tile) == "undefined") ? tile : tile[center_tile_y+rel_y]);
				if (typeof(tile) != "undefined") {
					if (tile.player_has_vision == "1") image = 'tile_current.png';
					else if (tile.player_has_vision == "0" && tile.cache != "") image = 'tile_outdated.png';
				}

				// change tile images as needed
				$('#map_tile'+ i + 'div').attr('src', 'media/themes/default/images/'+image);

				// update coordinates for all tiles
				$('#map_tile'+i+'coords').text('('+(center_tile_x+rel_x)+','+(center_tile_y+rel_y)+')');
				$('#map_tile_selector'+ i + 'div').attr('x', center_tile_x+rel_x);
				$('#map_tile_selector'+ i + 'div').attr('y', center_tile_y+rel_y);
				
				// helps with tile coordinates
				rel_x++;
			}

		}
		else
			alert(json_data.ERROR);
	});
}
