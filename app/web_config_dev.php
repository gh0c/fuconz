<?php

use app\helpers\Configuration;
// DB Config
Configuration::write('db.host', 'localhost');
Configuration::write('db.port', '');
Configuration::write('db.basename', 'fuconz');
Configuration::write('db.user', 'root');
Configuration::write('db.password', '****');
// Project Config
Configuration::write('path.url', '/fuconz/');



Configuration::write('mail.send', false);

Configuration::write('path.domain', 'http://localhost');