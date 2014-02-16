<?php

// This class defines the basic functionality needed to process and display a web-page.
class Page
{
	// Name of the main content for this page.
	// This string should match the name of some PHP file in WEBROOT/content/
	private $content_name;
	
	// HTML generated by the content file for this page.
	private $content_html;
	
	// Name of the page-layout to use for this page.
	private $layout;
	
	// Web-page window/tab title
	private $title = 'The Lost Colonies of Lygos';
	
	// Web-page description. Read by crawlers. Used on search engine result pages.
	private $meta_description = '[General, brief website description goes here]';
	
	
	// Class constructor
	// PHP Note: parameters which have assigned values may be omitted from the function call.
	function __construct($content_name = null, $layout = 'game')
	{
		// Assign a default value to the $content_name if it was not specified.
		if ( is_null($content_name) )
			$content_name = $_GET['p'];
		// PHP note: $_GET is an array of the variables from the URL string.
			
		$this->content_name = $this->clean_content_name($content_name);
		$this->layout = $layout;
	}
	
	// Run the computations needed for the specified page-content.
	public function execute()
	{
		global $Mysql, $Memcached, $User;
		
		// Begin capturing output to a buffer.
		ob_start();
		// Include the specified "content" file.
		include(WEBROOT .'content/'. $this->content_name .'.php');
		// Save buffer contents and clear buffer.
		$this->content_html = ob_get_clean();
		
		// note: the content file included above may change: 
		//		 $this->page_title, $this->meta_description, etc.
	}
	
	// Display the HTML for this web-page.
	public function render()
	{
		echo '<html>';
		
		echo '<head>';
		$this->head();
		echo '</head>';
		
		echo '<body>';
		$this->body();
		echo '</body>';
		
		echo '</html>';
	}
	
	// Print the HTML for the web-page's head.
	private function head()
	{
		echo '
			<head>
				<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
				<title>'. $this->page_title .'</title>
				<meta name="description" content="'. $this->meta_description .'">
				<!-- <link href="media/themes/default/favicon.png" rel="shortcut icon"/> -->
				<link rel="stylesheet" type="text/css" href="media/themes/no_theme/stylesheet.css" />
			</head>';
	}
	
	// Print the HTML for the web-page's body.
	private function body()
	{
		include(WEBROOT .'layouts/'. $this->layout .'1.php');
		echo $this->content_html;
		include(WEBROOT .'layouts/'. $this->layout .'2.php');
	}
	
	// If the input string is not a valid content string, return 'home'.
	// Else, return the inputted string.
	private function clean_content_name($s)
	{
		if ( preg_match('/^[a-z,A-Z,0-9,_]+$/', $s) != 1 )
			return 'home';
		else if ( !is_file(WEBROOT .'content/'. $s .'.php') )
		{
			// The requested content-file does not exist. Send them home instead.
			return 'home';
		}
		else
			return $s;
	}
	
	// Called when content of this page requires the user to be logged in.
	private function require_login()
	{
		global $User;
		
		if ( !$User->logged_in() )
		{
			// Save this page's URL variables.
			$_SESSION['post-login'] = $_SERVER['QUERY_STRING'];
			
			// Redirect to login page.
			header('Location: /?p=login');
			
			// Halt execution of the current page and script.
			exit();
		}
	}
}

?>