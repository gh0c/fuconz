<?php
$filters = glob('app/filters/*.filters.php');

foreach ($filters as $filter) {
    require $filter;
}