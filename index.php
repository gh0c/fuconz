<?php

/** LOADING & INITIALIZING BASE APPLICATION */

// Configuration for error reporting, useful to show every little problem during development
error_reporting(E_ALL);
ini_set("display_errors", 1);

date_default_timezone_set( "Europe/Zagreb" );

require 'app/start.php';
$app->run();


?>