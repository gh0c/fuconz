<?php

$app->get('/', function() use ($app) {
    $app->render('index.twig');
})->name('home');


$app->get('/lokacija', function() use ($app) {
    $app->render('location.twig');
})->name('location');

$app->get('/o-stranici', function() use ($app) {
    $app->render('about_site.twig');
})->name('about-site');


$app->get('/bootstrap-test', function() use ($app) {
    $app->render('bstest.twig');
})->name('bs');

?>