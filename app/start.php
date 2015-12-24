<?php

use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Slim\Middleware\SessionCookie;

use Noodlehaus\Config;
use RandomLib\Factory as RandomLib;
use app\helpers\Configuration;
use app\helpers\Hash;
use app\model\Middleware;


session_cache_limiter(false);
session_start();

//define ('INC_ROOT', dirname(__DIR__));
define('INC_ROOT', ".");
require INC_ROOT .'/vendor/autoload.php';
require INC_ROOT .'/app/extras/password.php';


require INC_ROOT .'/app/config.php';


$app = new Slim(array(
    'mode'=>file_get_contents(INC_ROOT . '/mode.php'),
    'view' => new Twig(INC_ROOT . '/app/views'),
    'templates.path' => INC_ROOT . '/app/views'
));

if ($app->mode === "development")
    require INC_ROOT . '/web_config_dev.php';
else if ($app->mode === "production")
    require INC_ROOT . '/web_config_prod.php';
if ($app->mode === "development2")
    require INC_ROOT . '/web_config_dev2.php';


// Cloudinary stuff
if (array_key_exists('REQUEST_SCHEME', $_SERVER)) {
    $cors_location = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] .
        dirname($_SERVER["SCRIPT_NAME"]) . Configuration::read("path.url") . "cloudinary_cors.html";
} else {
    $cors_location = "http://" . $_SERVER["HTTP_HOST"] . Configuration::read("path.url") . "cloudinary_cors.html";
}



$app->add(new Middleware\BeforeMiddleware());
$app->add(new Middleware\CsrfMiddleware());


if ($app->mode === "development**" && !$app->request->isAjax()) {
    $app->configureMode($app->config('mode'), function() use ($app) {

        // pre-application hook, performs stuff before real action happens @see http://docs.slimframework.com/#Hooks
        $app->hook('slim.before', function () use ($app) {
//
//            // SASS-to-CSS compiler @see https://github.com/panique/php-sass
//            SassCompiler::run("public/active_scss/", "public/css/");
//            // CSS minifier @see https://github.com/matthiasmullie/minify
//            $minifier = new MatthiasMullie\Minify\CSS('public/css/main.css');
//
//            $minifier->add('public/css/reset.css');
//            $minifier->add('public/css/general_classes.css');
//            $minifier->add('public/css/fonts.css');
//            $minifier->add('public/css/header.css');
//            $minifier->add('public/css/content.css');
//            $minifier->add('public/css/forms.css');
//            $minifier->add('public/css/calendar.css');
//            $minifier->add('public/css/legend.css');
//            $minifier->add('public/css/gmaps.css');
//            $minifier->add('public/css/match.css');
//            $minifier->add('public/css/footer.css');
//            $minifier->add('public/css/sideslide_menu.css');
//            $minifier->add('public/css/jquery-ui.custom_theme.css');
//            $minifier->add('public/css/jquery-ui.datepicker.struct.css');
//
//            $minifier->minify('public/css/style.css');
//
//
//            // --------------- !!! ----------------------
//            // --------------- !!! ----------------------
//            // UNCOMMENT AFTER CHANGING /admin/ STYLES!!!
//            // --------------- !!! ----------------------
//
//            SassCompiler::run("public/active_scss/admin/", "public/css/admin/");
//
//            $minifier = new MatthiasMullie\Minify\CSS('public/css/admin/main.css');
//            $minifier->add('public/css/admin/header.css');
//            $minifier->add('public/css/jquery-ui.custom_theme.css');
//            $minifier->add('public/css/jquery-ui.datepicker.struct.css');
//            $minifier->add('public/css/admin/tables.css');
//            $minifier->minify('public/css/admin/admin_style.css');
//             --------------- !!! ----------------------


            // JS minifier @see https://github.com/matthiasmullie/minify
            // DON'T overwrite your real .js files, always save into a different file
            //$minifier = new MatthiasMullie\Minify\JS('js/application.js');
            //$minifier->minify('js/application.minified.js');


            SassCompiler::run("public/code/scss/", "public/prod/css/");
            $minifier = new MatthiasMullie\Minify\CSS('public/prod/css/main.new.css');
            $minifier->minify('public/prod/css/min/style.css');
        });

    });
}


//Define a base variable for subfolder-based websites
$base = Configuration::read("path.url");
$app->base = $base;
$app->view->setData(array(
    'base' => $app->base,
    'cors_location' => $cors_location
));

require 'filters.php';
require 'routes.php';


// Compile SCSS etc..
$app->get('/cpl', function() use ($app) {
    echo "<pre>        Compiling...<br>       " . date("m.d. H:i:s") . "</pre>";
    SassCompiler::run("public/code/scss/", "public/prod/css/");
    $minifier = new MatthiasMullie\Minify\CSS('public/prod/css/main.new.css');
    $minifier->minify('public/prod/css/min/style.css');
    echo "<pre>        Done compiling!<br>       " . date("m.d. H:i:s") . "</pre>";
})->name('compile');

//

$app->auth = false;

$app->container->singleton('hash', function() use ($app) {
    return new Hash($app->config);
});

$view = $app->view();


$view->parserExtensions = array(
    new TwigExtension(),
    new Teraone\Twig\Extension\CloudinaryExtension()
);


