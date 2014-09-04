<?php
	session_start();
	ob_start();
	
	require('lib_std.php');
	
	if ( isset($_GET['page_element']) )
		require( clean_content_string($_GET['page_element']) . ".php" );
	else
	{
		require('lib_gameplay.php');
?>

<html>
<head>
	<?php require('head.php'); ?>
</head>
<body>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
	<?php
		require('layout1.php');
		
		// Include this page's inner content file.
		require( clean_content_string($content) . ".php" );

		require('layout2.php');
	?>
</body>
</html>

<?php
	}
	ob_end_flush();
?>