// This function is called when someone clicks the colony management link.
$('#link_div_colony_management').click(function() {
	// Grab the name of this screen
	var name = $(this).attr('id').substr(9);
	// Call the data-fetching script for this screen.
	request_data('game_screen_' + name, {"colony_id": colony_id}, function(json_data) {
		// Script successfully called.
		
		// Check to see if the script returned any warnings.
		if ( typeof json_data.WARNING != 'undefined' )
			alert('Warning: '+ json_data.WARNING);
		else
		{
			// No warnings occurred.
			// Erase any old contents on this screen.
			$('#buildings_container').html('');
			unselect_building();
			
			// Populate the content of this screen.
			for ( var i in json_data.buildings )
			{
				var building = json_data.buildings[i];
				building.update_cost = json_data.update_cost;
				buildings[building.type] = building;
				$('<img>', {
					"id": 'building_'+ building.type +'img',
					"class": 'building_img',
					"src": 'media/themes/'+ theme +'/images/building'+ building.type +'.png',
					"onclick": 'javascript:select_building('+ building.type +')'
				}).appendTo('#buildings_container');
			}
			
			// Display this screen.
			change_screen(name);
		}
	});
});


var bldg_original_x;
var bldg_original_y;
var bldg_original_z_index;
var selected_building_type;
function select_building(type)
{
	// Get rid of any prior building selections.
	unselect_building();
	
	// keep track of which building we're selecting so we can
	// undo the actions on it we're about to perform.
	selected_building_type = type;
	var building = buildings[type];
	
	// Move the building's image temporarily.
	bldg_original_x = $('#building_'+ type +'img').css('left');
	bldg_original_y = $('#building_'+ type +'img').css('top');
	bldg_original_z_index =  $('#building_'+ type +'img').css('z-index');
	var new_x = ($('#building_info_div1').width() / 2) - ($('#building_'+ type +'img').width() / 2);
	var new_y = 30;
	//var new_y = ($('#building_info_div1').height() / 2) - ($('#building_'+ type +'img').height() / 2);

	$('#building_'+ type +'img').animate({
		"left": new_x +"px",
		"top": new_y +"px",
		"z-index": 200
	});
	
	// Populate the info div
	$('#building_info_div2').html('');
	$('<div/>', {
		"id": 'building_title_div',
		"text": building.name
	}).appendTo('#building_info_div2');
	$('<div/>', {
		"id": "building_info_text_div"
	}).appendTo('#building_info_div2');
	
	$('<div/>', {
		"text": "level: "+ building.level
	}).appendTo('#building_info_text_div');
	$('<div/>', {
		"text": "maintenance: "
	}).appendTo('#building_info_text_div');
	$('<div/>', {
		"text": "upgrade cost: "+ building.update_cost.food
	}).appendTo('#building_info_text_div');
	$('<button/>', {
		"text": "upgrade ",
		"onclick": "javascript: upgrade_building('"+ colony_id +"', '"+ building.id +"', '"+ building.type +"' ); $(this).attr('disabled', 'disabled');"
	}).appendTo('#building_info_text_div');
	
	
	// animate the opening of div1.
	var building_info_div1_height = $('#building_info_div1').height();
	$('#building_info_div1').css("height", "1px");
	$('#building_info_div1').show();
	$('#building_info_div1').animate({
		"height": building_info_div1_height +"px"
	}, function() {
		$('#unselect_bldg_btn').show();
		
		// animate the opening of div2.
		var building_info_div2_width = $('#building_info_div2').width();
		$('#building_info_div2').css("width", "1px");
		$('#building_info_div2').show();
		$('#building_info_div2').animate({
			"width": building_info_div2_width +"px"
		});
	});
	
	$('#building_'+ selected_building_type +'img').attr("onclick", "unselect_building()");
}


function unselect_building()
{
	$('#building_'+ selected_building_type +'img').animate({
		"left": bldg_original_x,
		"top": bldg_original_y,
		"z-index": bldg_original_z_index
	});
	$('#building_'+ selected_building_type +'img').attr("onclick", "select_building("+ selected_building_type +")");
	
	$('#building_info_div1').hide();
	$('#building_info_div2').hide();
	
	$('#unselect_bldg_btn').hide();
	
	selected_building_type = false;
}


function upgrade_building(colony_id, building_id, building_type)
{
	request_data('upgrade_building', 
		{
			"colony_id": colony_id, 
			"building_id": building_id, 
			"building_type": building_type
		}, 
		function(json_data) {
			// Script successfully called.
			// Check for warnings.
			if ( typeof json_data.WARNING != 'undefined' )
			{
				if ( json_data.WARNING == 'insufficient_resources' )
					alert('You do not have enough resources to perform the requested upgrade.');
				else
					alert(json_data.WARNING);
			}
			else
			{
				// No warnings were returned by the script.
				fetch_jobs_queue();
				refresh_resources_display();
			}
		}
	);
}