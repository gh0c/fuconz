<?php

use app\helpers\Configuration;

$db_params = parse_url(getenv('DATABASE_URL'));
// DB Config
Configuration::write('db.type', "pgsql");

Configuration::write('db.encoding', 'client_encoding');
Configuration::write('db.host', $db_params["host"]);
Configuration::write('db.port', $db_params["port"]);
Configuration::write('db.basename', ltrim($db_params["path"],'/'));
Configuration::write('db.user', $db_params["user"]);
Configuration::write('db.password', $db_params["pass"]);
// Project Config
Configuration::write('path.url', '/');



Configuration::write('mail.send', false);

Configuration::write('path.domain', 'https://fuconz.herokuapp.com/'); // your domain


Configuration::write('cl.images.path', 'fuconz/prod/images/');


\Cloudinary::config(array(
    "cloud_name" => "dqpjjihsv",
    "api_key" => "246759957442647",
    "api_secret" => "3jQQ4ITKsKslrlwNucryALiYuU0"
));