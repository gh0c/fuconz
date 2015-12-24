<?php

/** LOADING & INITIALIZING BASE APPLICATION */

// Configuration for error reporting, useful to show every little problem during development
error_reporting(E_ALL);
ini_set("display_errors", 1);

date_default_timezone_set( "Europe/Zagreb" );
set_time_limit ( 50 );

try {
    require 'app/start.php';
    $app->run();

} catch (\Exception $e) {echo "Greška: " . $e->getMessage();}

