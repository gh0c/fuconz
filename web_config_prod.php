<?php

use app\helpers\Configuration;
// DB Config
Configuration::write('db.host', 'localhost');
Configuration::write('db.port', '');
Configuration::write('db.basename', 'authdatabase');
Configuration::write('db.user', 'root');
Configuration::write('db.password', '***');
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