<?php

require('vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

use Phpmig\Adapter;
use Pimple\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use instms\config\Settings;

$container = new Container;
$settings = new Settings;

$container['config'] = [
    'driver'    => $settings->dbDriver,
    'host'      => $settings->dbHost,
    'port'      => $settings->dbPort,
    'database'  => $settings->dbName,
    'username'  => $settings->dbUser,
    'password'  => $settings->dbPassword,
    'charset'   => $settings->dbCharset,
    'collation' => $settings->dbCollation,
    'prefix'    => $settings->dbPrefix,
];

$container['db'] = function ($c) {
    $capsule = new Capsule();
    $capsule->addConnection($c['config']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

   return $capsule;
};

$container['phpmig.adapter'] = function($c) {
    return new Adapter\Illuminate\Database($c['db'], 'migrations');
};

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

$container['db.schema'] = function() {
    return Capsule::schema();
};

return $container;