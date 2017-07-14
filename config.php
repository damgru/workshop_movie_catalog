<?php
define('DEBUG_MODE', true);

//define('DB_HOST', '172.17.0.1');
define('DB_HOST', '52.16.150.144');
define('DB_PORT', 3306);
define('DB_USER', 'workshop');
define('DB_PASS', 'workshop');
define('DB_NAME', 'movie_catalog');

//http://192.168.96.125:32781/
//define('RABBITMQ_HOST', '192.168.96.125');
//define('RABBITMQ_PORT', 32778);
define('RABBITMQ_HOST', '52.16.150.144');
define('RABBITMQ_PORT', 5672);
define('RABBITMQ_USER', 'guest');
define('RABBITMQ_PASS', 'guest');
define('RABBITMQ_VHOST', '/');

define('THUMB_HOST', 'http://192.168.96.170:8080');
