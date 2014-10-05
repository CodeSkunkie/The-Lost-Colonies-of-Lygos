
// Send an AJAX request for JSON. If no errors, send JSON to callback_function.
// Example calls to this function:
//	request_data('hello', function(json) {
//		console.log(json);
//	});
//
//	request_data('echo', {"message":"hi"}, function(json) {
//		console.log(json);
//	});
function request_data(script_name, input, callback_function)
{
	// Dynamically check to see which of the optional parameters is specified.
	if ( typeof( input ) == 'function' )
	{	
		callback_function = input;
		input = {};
	}
	
	// Send the AJAX request.
	var request_url = 'ajax/index.php?script='+ script_name;
	$.getJSON(request_url, input)
	.done( function(json) {
		// Call successful.
		
		if ( json.ERROR != '' )
			request_error_handler(json.ERROR, script_name);
		else
			return callback_function(json);
	})
	.fail(function() {
		request_error_handler('AJAX_failed', script_name);
	})
	
}

// This function attempts to gracefully handle any errors that may have 
// occured during an AJAX request.
function request_error_handler(error, script_name)
{
	// Handle individual errors here.
	if ( error == 'db_connect' )
		alert('ERROR: Could not process request at this time. Database unavailable.');
	else if ( error == 'script_not_found' )
		alert('ERROR: The script specified to handle this request does not exist.');
	else if ( error == 'login_required' )
		alert('ERROR: Your session has timed out. Please log in again.');
	else if ( error == 'dirty_script_name' )
		alert('ERROR: Invalid characters in script name: '+ script_name);
	else if ( error == 'AJAX_failed' )
		alert('ERROR: The AJAX request could not be completed: '+ script_name);
	else
		alert('ERROR: '+ error);
}

function time() 
{
	return Math.round(new Date().getTime() / 1000);
}

// Return a formatted string representing some amount of time.
// Input is in the form of seconds.
// Result format: HH:MM:SS
function format_time_duration(total_seconds) {
	var seconds = total_seconds % 60;
	var total_minutes = Math.floor(total_seconds / 60);
	var minutes = total_minutes % 60;
	var hours = Math.floor(total_minutes / 60);
	
	return ("00" + hours).substr(-2,2) +":"+ ("00" + minutes).substr(-2,2) +":"+ 
		("00" + seconds).substr(-2,2);
}

// Return the distance between to hexagonal tiles.
// Note: at this time, the database and map call the z coordinate y.
//		This function should be given the "x and y" coordinates from
//		the database.
function hex_distance(x1, z1, x2, z2) {
	// Convert to cube-coordinates first.
	// Derive the missing coordinate.
    y1 = -x1 - z1;
    y2 = -x2 - z2;
    return (Math.abs(x1 - x2) + Math.abs(y1 - y2) + Math.abs(z1 - z2)) / 2;
}