<?php

use app\helpers\Configuration;
// DB Config
Configuration::write('db.type', "mysql");

Configuration::write('db.encoding', 'charset');

Configuration::write('db.host', 'localhost');
Configuration::write('db.port', '');
Configuration::write('db.basename', 'fuconz');
Configuration::write('db.user', 'root');
Configuration::write('db.password', 'gh0c2211hrx');
// Project Config
Configuration::write('path.url', '/fuconz/');



Configuration::write('mail.send', false);

Configuration::write('path.domain', 'http://localhost');


Configuration::write('cl.images.path', 'fuconz/');


\Cloudinary::config(array(
    "cloud_name" => "dqpjjihsv",
    "api_key" => "246759957442647",
    "api_secret" => "3jQQ4ITKsKslrlwNucryALiYuU0"
));