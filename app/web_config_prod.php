<?php

use app\helpers\Configuration;
// DB Config
Configuration::write('db.host', 'localhost');
Configuration::write('db.port', '');
Configuration::write('db.basename', 'authdatabase');
Configuration::write('db.user', 'root');
Configuration::write('db.password', 'databasepass');
// Project Config
Configuration::write('path.url', '/');



Configuration::write('mail.send', false);

Configuration::write('path.domain', 'http://....'); // your domain