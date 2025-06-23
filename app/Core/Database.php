<?php

namespace App\Core;

use Medoo\Medoo;

class Database
{
    /** @var Medoo|null */
    private static $instance = null;

    public static function connect(): Medoo
    {


        if (self::$instance === null) {
            $env = parse_ini_file(__DIR__ . '/../../.env');

            self::$instance = new Medoo([
                'type'     => $env['DB_TYPE'],
                'database' => $env['DB_PATH'],
                'driver'     => $env['DB_DRIVER'],
                'path'     => $env['DB_TYPE'] === 'sqlite' ? $env['DB_PATH'] : null,
                'host'     => $env['DB_TYPE'] !== 'sqlite' ? $env['DB_HOST'] : null,
                'username' => $env['DB_USER'],
                'password' => $env['DB_PASSWORD'],
                'charset'  => 'utf8mb4'
            ]);
        }

        return self::$instance;
    }
}
