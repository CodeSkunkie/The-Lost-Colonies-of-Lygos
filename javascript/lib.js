
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
	var json_data = '';
	$.getJSON(request_url, input)
	.done( function(json) {
		// Call successful.
		if ( json.ERROR != '' )
			request_error_handler(json.ERROR);
		else
			return callback_function(json);
	})
	.fail(function() {
		request_error_handler('AJAX_failed');
	})
	
}

// This function attempts to gracefully handle any errors that may have 
// occured during an AJAX request.
function request_error_handler(error)
{
	// Handle individual errors here.
	if ( error == 'db_connect' )
		alert('ERROR: Could not process request at this time. Database unavailable.');
	else if ( error == 'script_not_found' )
		alert('ERROR: The script specified to handle this request does not exist.');
	else if ( error == 'login_required' )
		alert('ERROR: The script specified to handle this request does not exist.');
	else if ( error == 'dirty_script_name' )
		alert('ERROR: Invalid characters in script name.');
	else if ( error == 'AJAX_failed' )
		alert('ERROR: The AJAX request could not be completed.');
}