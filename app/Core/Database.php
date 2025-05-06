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
                'database' => $env['DB_NAME'],
                'host'     => $env['DB_TYPE'] !== 'sqlite' ? $env['DB_HOST'] : null,
                'username' => $env['DB_TYPE'] !== 'sqlite' ? $env['DB_USER'] : null,
                'password' => $env['DB_TYPE'] !== 'sqlite' ? $env['DB_PASS'] : null,
                'charset'  => 'utf8mb4'
            ]);
        }

        return self::$instance;
    }
}
