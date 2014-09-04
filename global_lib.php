<?php

// Print a line break.
function br()
{
	print "<br />";
}

// Print text on a new line
function echon($string)
{
	echo $string ."\n<br />";
}

// Print text on a new line
function necho($string)
{
	echo "\n<br />". $string;
}

// Print text on a new line
function nechon($string)
{
	echo "\n<br />". $string ."\n<br />";
}

// Print out an array.
function print_arr($arr)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

// Alias for a common fucnction whose name is too long.
function clean_sql($input)
{
	return mysql_real_escape_string($input);
}

function clean_number( $num )
{
	if ( !empty($num) && !is_numeric($num) )
	{
		header('Location: /');
		exit();
	}
	else
		return $num;
}

function clean_text( $text )
{
	return htmlentities($text, ENT_QUOTES);
}

//
function forward_domain($from, $to)
{
	$new_url = 'http://www.'. $to . $_SERVER['PHP_SELF'];
	if(!empty($_GET))
	{
		$new_url .= '?';
		foreach ($_GET as $key => $value)
		{
			$new_url .= $key .'='. $value .'&';
		}
		$new_url = substr($new_url, 0, strlen($new_url)-1);
	}
	header('Location: '. $new_url, true, 301);
	exit();
}

function magic_quotes_disabled()
{
	return true;
}

?>