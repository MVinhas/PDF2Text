<?php

namespace PDF2Text\Core;

use Dotenv\Dotenv;

class Config
{
    private static ?Config $instance = null;
    private array $config = [];
    private bool $isTestEnvironment = false;

    private function __construct()
    {
        if ($this->detectTestEnvironment()) {
            $this->setTestEnvironment(true);
        } else {
            $this->loadEnvironment();
            $this->loadConfig();
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function reset(): void
    {
        self::$instance = null;
        $this->config = [];
        $this->loadEnvironment();
        $this->loadConfig();
    }

    public function setTestEnvironment(bool $isTest = true): void
    {
        $this->isTestEnvironment = $isTest;
        if ($isTest) {
            $this->loadTestConfig();
        } else {
            $this->loadConfig();
        }
    }

    private function detectTestEnvironment(): bool
    {
        // Automatically detect PHPUnit or CLI-based test runners
        return (php_sapi_name() === 'cli' && (
            defined('PHPUNIT_RUNNING') ||
            isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing' ||
            getenv('APP_ENV') === 'testing' ||
            isset($_SERVER['argv'])
            && implode(' ', $_SERVER['argv']) !== ''
            && preg_match('/phpunit|pest/', implode(' ', $_SERVER['argv']))
        ));
    }

    private function loadEnvironment(): void
    {
        if (!$this->isTestEnvironment) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->safeLoad();
        }
    }

    private function loadConfig(): void
    {
        $this->config = [
            'app' => [
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
            ],
            'security' => [
                'session' => [
                    'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 120),
                    'secure' => filter_var($_ENV['SESSION_SECURE'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'httponly' => filter_var($_ENV['SESSION_HTTPONLY'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'samesite' => $_ENV['SESSION_SAMESITE'] ?? 'Lax',
                ],
            ],
            'upload' => [
                'max_file_size' => (int)($_ENV['MAX_FILE_SIZE'] ?? 268435456),
                'max_allowed_chars' => (int)($_ENV['MAX_ALLOWED_CHARS'] ?? 1000000),
                'temp_dir' => $_ENV['UPLOAD_TEMP_DIR'] ?? '/tmp/pdf2text',
            ],
            'logging' => [
                'level' => $_ENV['LOG_LEVEL'] ?? 'error',
                'channel' => $_ENV['LOG_CHANNEL'] ?? 'file',
                'file' => $_ENV['LOG_FILE'] ?? 'logs/app.log',
            ],
            'pdf' => [
                'processor' => $_ENV['PDF_PROCESSOR'] ?? 'pdftotext',
                'processor_options' => $_ENV['PDF_PROCESSOR_OPTIONS'] ?? '-layout -raw -enc UTF-8',
            ],
            'rate_limit' => [
                'enabled' => filter_var($_ENV['RATE_LIMIT_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'attempts' => (int)($_ENV['RATE_LIMIT_ATTEMPTS'] ?? 10),
                'decay_minutes' => (int)($_ENV['RATE_LIMIT_DECAY_MINUTES'] ?? 1),
            ],
        ];
    }

    private function loadTestConfig(): void
    {
        $this->config = [
            'app' => [
                'env' => 'testing',
                'debug' => true,
                'url' => 'http://localhost:8000',
            ],
            'security' => [
                'session' => [
                    'lifetime' => 120,
                    'secure' => false,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ],
            ],
            'upload' => [
                'max_file_size' => 268435456,
                'max_allowed_chars' => 1000000,
                'temp_dir' => sys_get_temp_dir(),
            ],
            'logging' => [
                'level' => 'debug',
                'channel' => 'file',
                'file' => 'logs/test.log',
            ],
            'pdf' => [
                'processor' => 'pdftotext',
                'processor_options' => '-layout -raw -enc UTF-8',
            ],
            'rate_limit' => [
                'enabled' => true,
                'attempts' => 10,
                'decay_minutes' => 1,
            ],
        ];
    }

    public function isTestEnvironment(): bool
    {
        return $this->isTestEnvironment;
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function all(): array
    {
        return $this->config;
    }
}
