<?php

namespace PDF2Text\Utils;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use PDF2Text\Core\Config;

class Logger
{
    private static ?MonologLogger $logger = null;
    private static Config $config;

    public static function getInstance(): MonologLogger
    {
        if (self::$logger === null) {
            self::$config = Config::getInstance();
            self::initializeLogger();
        }
        return self::$logger;
    }

    private static function initializeLogger(): void
    {
        $logger = new MonologLogger('pdf2text');

        // Set up the formatter
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
        $formatter = new LineFormatter($output, $dateFormat);

        // Add rotating file handler
        $logFile = self::$config->get('logging.file', 'logs/app.log');
        $maxFiles = 7; // Keep logs for 7 days
        $fileHandler = new RotatingFileHandler($logFile, $maxFiles, self::getLogLevel());
        $fileHandler->setFormatter($formatter);
        $logger->pushHandler($fileHandler);

        // Add error log handler for critical errors
        $errorLogHandler = new StreamHandler('php://stderr', MonologLogger::ERROR);
        $errorLogHandler->setFormatter($formatter);
        $logger->pushHandler($errorLogHandler);

        self::$logger = $logger;
    }

    private static function getLogLevel(): int
    {
        $level = strtoupper(self::$config->get('logging.level', 'error'));
        return match ($level) {
            'DEBUG' => MonologLogger::DEBUG,
            'INFO' => MonologLogger::INFO,
            'NOTICE' => MonologLogger::NOTICE,
            'WARNING' => MonologLogger::WARNING,
            'ERROR' => MonologLogger::ERROR,
            'CRITICAL' => MonologLogger::CRITICAL,
            'ALERT' => MonologLogger::ALERT,
            'EMERGENCY' => MonologLogger::EMERGENCY,
            default => MonologLogger::ERROR,
        };
    }

    public static function emergency(string $message, array $context = []): void
    {
        self::getInstance()->emergency($message, $context);
    }

    public static function alert(string $message, array $context = []): void
    {
        self::getInstance()->alert($message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::getInstance()->critical($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::getInstance()->error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::getInstance()->warning($message, $context);
    }

    public static function notice(string $message, array $context = []): void
    {
        self::getInstance()->notice($message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::getInstance()->info($message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::getInstance()->debug($message, $context);
    }
}
