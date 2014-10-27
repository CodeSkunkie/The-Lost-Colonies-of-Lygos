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
			hide_unbuilt_buildings_menu();
			
			// Populate the content of this screen.
			for ( var i in json_data.buildings )
			{
				var building = json_data.buildings[i];
				buildings[building.type] = building;
				buildings_method_data[building.type] = json_data.buildings_method_data[i];
				$('<img>', {
					"id": 'building_'+ building.type +'img',
					"class": 'building_img',
					"src": 'media/themes/'+ theme +'/images/building'+ building.type +'.png',
					"onclick": 'javascript:select_building('+ building.type +')'
				}).appendTo('#buildings_container');
			}
			$('<div/>', {
				"text": "[build new modules]",
				"id": "build_new_buildings_link",
				"onclick": "show_unbuilt_buildings_menu();"
			}).appendTo('#buildings_container');
			
			
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
	var building_method_data = buildings_method_data[type];
	
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
		"text": building.long_descript
	}).appendTo('#building_info_text_div')
	$('<div/>', {
		"text": "level "+ building.level
	}).appendTo('#building_info_text_div');;
	$('<div/>', {
		"html": "current hourly resource consumption: "+ resource_upkeep_html(building_method_data['upkeep1'])
	}).appendTo('#building_info_text_div');
	$('<div/>', {
		"html": "upgrade cost: "+ resource_bundle_html(building_method_data['cost'])
	}).appendTo('#building_info_text_div');
	$('<div/>', {
		"html": "additional hourly resource consumption: "+ resource_upkeep_html(building_method_data['upkeep2'])
	}).appendTo('#building_info_text_div');
	$('<div/>', {
		"html": "construction duration: "+ format_time_duration(building_method_data['duration'])
	}).appendTo('#building_info_text_div');
	$('<button/>', {
		"text": "upgrade ",
		"onclick": "javascript: upgrade_building('"+ colony_id +"', '"+ building.id +"', '"+ building.type +"' ); $(this).attr('disabled', 'disabled');"
	}).appendTo('#building_info_text_div');
	if(building.type == 5){
		$('<button/>', {
			"text": "research ",
			"onclick": "javascript: upgrade_research();"
		}).appendTo('#building_info_text_div');
	}
	
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

function upgrade_research()
{
	$('#research').html('');
	$('#research').show();	
	$('#building_info_text_div').hide();	

	$('<div/>', {
		"id": 'research',
	}).appendTo('#building_info_div2');

	$('<button/>', {
		"class": "back_button",
		"text": "<- ",
		"onclick": "javascript: back_research();"
	}).appendTo('#research');

	/*$('<div/>', {
		"id": "research_title",
		"text": "Ship Upgrades"
	}).appendTo('#research');*/
	$('<select/>', {
		"id": "research_title",
		"onchange": "javascript: change_research();"
	}).appendTo('#research');

	var select = document.getElementById("research_title");
    var option = document.createElement('option');
    option.text = "Miscellaneous Upgrades";
    option.value = "Miscellaneous";
    select.add(option, 0);
    var option = document.createElement('option');
    option.text = "Building Upgrades";
    option.value = "Building";
    select.add(option, 0);
    var option = document.createElement('option');
    option.text = "Ship Upgrades";
    option.value = "Ship";
    select.add(option, 0);

	var ship_upgrades = [
	    'Fighter',
	    'Scout',
	    'Cargo',
	    'Tank'
	];
	for (var i = 0; i < ship_upgrades.length; i++)
	{
		$('<img>', {
			"id": 'research_'+ ship_upgrades[i] +'_img',
			"class": 'research_img',
			"src": 'media/themes/'+ theme +'/images/'+ ship_upgrades[i] +'_icon.png',
			"onclick": "javascript: select_research('"+ colony_id +"', '"+ ship_upgrades[i] +"')"
		}).appendTo('#research');
	}

}

function select_research(colony_id, research_type){
	request_data('construct_research', 
		{
			"colony_id": colony_id,
			"research_type": research_type
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

function change_research(){
	var title = document.getElementById("research_title");
	var research = title.options[title.selectedIndex].value;
	console.log(research);
}

function back_research(){
	$('#research').hide();
	$('#building_info_text_div').show();
}


function construct_building(colony_id, building_type)
{
	request_data('construct_building', 
		{
			"colony_id": colony_id,
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

function show_unbuilt_buildings_menu()
{
	request_data('unbuilt_buildings', {"colony_id": colony_id}, function(json_data) {
		
		$('#unbuilt_building_list').html('');
		for ( var i in json_data.buildings )
		{
			var building = json_data.buildings[i];
			var cost = json_data.cost[i];
			var upkeep = json_data.upkeep[i];
			var duration = json_data.duration[i];
			//console.log(building);
			$('<div/>', {
				"id": "unbuilt_building"+ i +"_div",
				"class": "unbuilt_building_div"
			}).appendTo('#unbuilt_building_list');
			$('<div/>', {
				"id": "unbuilt_building"+ i +"_img_div",
				"class": "unbuilt_building_img_div"
			}).appendTo('#unbuilt_building'+ i +'_div');
			$('<img/>', {
				"class": "unbuilt_building_img",
				"src": "media/themes/"+ theme +"/images/building"+ building.type +".png", 
				"width": "110"
			}).appendTo('#unbuilt_building'+ i +'_img_div');
			$('<div/>', {
				"id": "unbuilt_building"+ i +"_info_div",
				"class": "unbuilt_building_info_div"
			}).appendTo('#unbuilt_building'+ i +'_div');
			$('<div/>', {"style": "clear:left;"}).appendTo('#unbuilt_building'+ i +'_div');
			$('<div/>', {
				"class": "unbuilt_building_title",
				"text": building.name
			}).appendTo('#unbuilt_building'+ i +'_info_div');
			$('<div/>', {
				"class": "unbuilt_building_descript",
				"text": building.long_descript
			}).appendTo('#unbuilt_building'+ i +'_info_div');
			$('<div/>', {
				"class": "unbuilt_building_cost",
				"html": 'construction cost: '+ resource_bundle_html(cost)
			}).appendTo('#unbuilt_building'+ i +'_info_div');
			$('<div/>', {
				"class": "unbuilt_building_upkeep",
				"html": "additional upkeep: "+ resource_upkeep_html(upkeep)
			}).appendTo('#unbuilt_building'+ i +'_info_div');
			$('<div/>', {
				"class": "unbuilt_building_duration",
				"html": "construction time: "+ format_time_duration(duration)
			}).appendTo('#unbuilt_building'+ i +'_info_div');
			$('<button/>', {
				"text": "construct module ",
				"onclick": "javascript: construct_building('"+ colony_id +"', '"+ building.type +"' ); $(this).attr('disabled', 'disabled');"
			}).appendTo('#unbuilt_building'+ i +'_info_div');
			$('<div/>', {"style": "clear:left;"}).appendTo('#unbuilt_building_list');
		}
		
		// Display the whole menu at once with an animation.
		var unbuilt_building_menu_height= $('#unbuilt_building_menu').height();
		$('#unbuilt_building_menu').css('height', '1px');
		$('#unbuilt_building_menu').show();
		$('#unbuilt_building_menu').animate({
			"height": unbuilt_building_menu_height +"px"});
	});
}

function hide_unbuilt_buildings_menu() {
	$('#unbuilt_building_menu').hide();
}