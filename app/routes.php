<?php
$routers = glob('app/routes/*.router.php');

foreach ($routers as $router) {
    require $router;
}