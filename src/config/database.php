<?php

use Illuminate\Support\Str;

$app_connections = [];
if (env('DB_ACCOUNT_CONNECTIONS')) {
    $connections = explode(',', env('DB_ACCOUNT_CONNECTIONS'));
    foreach ($connections as $connection) {
        $app_connections[$connection] = [
            'driver' => env('DB_' . strtoupper($connection) . '_DRIVER', 'mysql'),
            'host' => env('DB_' . strtoupper($connection) . '_HOST', '127.0.0.1'),
            'port' => env('DB_' . strtoupper($connection) . '_PORT', 3306),
            'username' => env('DB_' . strtoupper($connection) . '_USERNAME', 'root'),
            'password' => env('DB_' . strtoupper($connection) . '_PASSWORD', ''),
            'unix_socket' => env('DB_' . strtoupper($connection) . '_SOCKET', ''),
            'charset' => env('DB_' . strtoupper($connection) . '_CHARSET', 'utf8mb4'),
            'collation' => env('DB_' . strtoupper($connection) . '_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ];
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'app_pyme'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => array_merge([

        'app_panel' => [
            'driver' => 'mysql',
            'url' => env('DB_CONTROL_URL'),
            'host' => env('DB_CONTROL_HOST', '127.0.0.1'),
            'port' => env('DB_CONTROL_PORT', '3306'),
            'database' => env('DB_CONTROL_DATABASE', 'laravel'),
            'username' => env('DB_CONTROL_USERNAME', 'root'),
            'password' => env('DB_CONTROL_PASSWORD', ''),
            'unix_socket' => env('DB_CONTROL_SOCKET', ''),
            'charset' => env('DB_CONTROL_CHARSET', 'utf8mb4'),
            'collation' => env('DB_CONTROL_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'app_pyme' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'legacy' => [
            'driver' => 'mysql',
        ],

    ], $app_connections),

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
