<?php

namespace App\Helpers;

class LogHelper
{
    private const LOG_DIR = __DIR__ . '/../../storage/logs';
    private const LOG_FILE = 'app.log';

    public static function info(string $message, array $context = []): void
    {
        self::writeLog('INFO', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::writeLog('ERROR', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::writeLog('DEBUG', $message, $context);
    }

    private static function writeLog(string $level, string $message, array $context = []): void
    {
        try {
            if (!is_dir(self::LOG_DIR)) {
                mkdir(self::LOG_DIR, 0775, true);
            }

            $date = date('Y-m-d H:i:s');
            $contextStr = $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
            $line = "[$date] $level: $message" . ($contextStr ? " | $contextStr" : "") . PHP_EOL;

            file_put_contents(self::LOG_DIR . '/' . self::LOG_FILE, $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            error_log("Failed to write to log: " . $e->getMessage());
        }
    }
}
