<?php
use Doctrine\DBAL\Configuration;

require_once 'vendor/autoload.php';
require_once 'config.php';

$config = new Configuration();
$connectionParams = array(
    'host' => DB_HOST,
    'port' => DB_PORT,
    'user' => DB_USER,
    'password' => DB_PASS,
    'dbname' => DB_NAME,
    'driver' => 'pdo_mysql'
);
$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
$pdo = $connection->getWrappedConnection();

$config = array(
    'environments' => array(
        'default_database' => 'development',
        'development' => array(
            'name' => DB_NAME,
            'connection' => $pdo
        )
    ),
    'paths' => array(
        'migrations' => "%%PHINX_CONFIG_DIR%%/src/Migrations",
        'seeds' => "%%PHINX_CONFIG_DIR%%/src/Migrations/seeds"
    )
);

return $config;