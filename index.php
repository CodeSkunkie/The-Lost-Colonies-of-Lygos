<?php

session_start();

// The directory path for this file on the web-server:
define('WEBROOT', '/var/www/');


require('core_classes.php');


// Create a mysql object to query with the database.
$Mysql = new mysqli("127.0.0.1", "lygos", "3o_e}.890h.23._lm", "lygos");
if ($Mysql->connect_errno)
    exit('Could not connect to Lygos database. Please try again later.');

// Create memcached object to read and write data to the cache.
$Memcached = new Memcached();
$Memcached->addServer('localhost', 11211);

$User = new User();

// An example of how to use the Page class to create a web-page:
$Page = new Page($_GET['p'], 'game');
$Page->execute();
$Page->render();


//echo '<br /><br />debugging output:<br />';
//print_arr($_SESSION);
//print_arr($_COOKIE);


?>