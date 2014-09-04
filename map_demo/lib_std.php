<?php

// Use this to interact with the specific bot database:
$db_link = mysql_connect("127.0.0.1", "lygos", "3o_e}.890h.23._lm");
if ($db_link === FALSE)
{
	echo "Unable to connect to the database at this time. Please check back later";
	exit();
}
mysql_selectdb("lygos", $db_link);

// Initialize some commonly used variables.
$content = clean_content_string($_GET['content']);
$_SESSION['user_id'] = 1; // auto-login as admin

// Save the current URL in the session.
if (isset($current_url))
{
	$_SESSION['prev_url'] = $_SESSION['current_url'];
	$prev_url = $_SESSION['prev_url'];
}
$_SESSION['current_url'] = current_url();

function me()
{
	return ($_SERVER['REMOTE_ADDR'] == '76.101.254.151');
}

function error_text($errorText)
{
	return "<font size=4 color=\"#FF8888\">". $errorText . "</font><br><br>";
}

// Returns 'home' if the content string is not safe or valid.
// Otherwise it returns the content string.
function clean_content_string($content)
{
	$safe_char_codes = array(
		// Numbers 0-9
		48, 49, 50, 51, 52, 53, 54, 55, 56, 57,
		// Letters A-Z
		65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82,
			83, 84, 85, 86, 87, 88, 89, 90, 
		// Letters a-z
		97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111,
			112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122,
		// Underscore _
		95);

	// Check for "dangerous" characters in the content string.
	for ($i = 0; $i < strlen($content); $i++)
		if ( !in_array( ord($content[$i]), $safe_char_codes ) )
			$content = 'home'; // Bad character detected.
	
	// Check if the requested content file exists.
	if ( !is_file($content . ".php") )
		$content = 'home';
		
	// Check to see if content is even set
	if ( !isset($content) || $content == '' || empty($content) )
		$content = 'home';
	
	return $content;
}

// Return the current GET variables string.
function http_get_str()
{
	if(!empty($_GET))
		$var_str = '?';
	foreach ($_GET as $key => $value)
	{
		$var_str .= $key .'='. $value .'&';
	}
	if(!empty($_GET))
		$var_str = substr($var_str, 0, strlen($var_str)-1);
	return $var_str;
}

// Return the current URL including get variables.
function current_url()
{
	$url = 'http://priceterminal.com/';
	$url .= http_get_str();
	return $url;
}

function on_site($url)
{
	$check_for = 'http://priceterminal.com';
	if( substr($url, 0, strlen($check_for)) == $check_for )
		return true;
	else
		return false;
}

function go_home()
{
	header('Location: ?content=home');
	exit();
}

?>