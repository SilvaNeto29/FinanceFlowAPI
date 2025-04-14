<?php

namespace App\Core;

use Medoo\Medoo;

class Database
{
    private static $instance = null;

    public static function connect(): Medoo
    {
        if (self::$instance === null) {
            $env = parse_ini_file(__DIR__ . '/../../.env');

            self::$instance = new Medoo([
                'type'     => $env['DB_TYPE'],
                'host'     => $env['DB_HOST'],
                'database' => $env['DB_NAME'],
                'username' => $env['DB_USER'],
                'password' => $env['DB_PASS'],
                'charset'  => 'utf8mb4'
            ]);
        }

        return self::$instance;
    }
}
