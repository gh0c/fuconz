<?php

if (!defined('TWIG_LIB_DIR') || 'NOT_SET' === TWIG_LIB_DIR) {
    die('The path to the Twig lib/ directory must be defined in phpunit.xml configuration.');
}
if (!defined('CLOUDINARY_URL') || 'NOT_SET' === CLOUDINARY_URL) {
    die('The cloudinary url must be defined in phpunit.xml configuration.');
}

require_once TWIG_LIB_DIR.'/Twig/Autoloader.php';
Twig_Autoloader::register();

require_once __DIR__.'/../vendor/autoload.php';
