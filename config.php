<?php
// Security configuration
const MAX_FILE_SIZE = 256 * 1024 * 1024; // 256MB - Servers usually don't support more than this
const MAX_ALLOWED_CHARS = 1_000_000; // 1 million characters
const SESSION_TIMEOUT = 1800; // 30 minutes
const ALLOWED_MIME_TYPES = ['application/pdf'];

// Check for required dependencies
function checkDependencies(): void {
    $autoloadPath = __DIR__ . '/vendor/autoload.php';
    
    try {
        if (!@include_once $autoloadPath) {
            throw new RuntimeException("Could not include autoload.php");
        }
    } catch (Throwable $e) {
        // Debug information
        $debugInfo = [
            'autoload_path' => $autoloadPath,
            'current_dir' => __DIR__,
            'file_exists' => file_exists($autoloadPath),
            'is_readable' => is_readable($autoloadPath),
            'is_file' => is_file($autoloadPath),
            'file_perms' => file_exists($autoloadPath) ? substr(sprintf('%o', fileperms($autoloadPath)), -4) : 'N/A',
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown',
            'error' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ];
        
        logError("Dependency check failed", $debugInfo);
        
        $_SESSION['last_error'] = sprintf(
            "Failed to load required dependencies. Error: %s\nPath checked: %s\nDebug info: %s",
            $e->getMessage(),
            $autoloadPath,
            json_encode($debugInfo, JSON_PRETTY_PRINT)
        );
        include 'error.php';
        exit(1);
    }
}

// Security headers
function setSecurityHeaders(): void {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline';");
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}

// Session security
function secureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    if (empty($_SESSION['last_activity']) || time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_regenerate_id(true);
    }
    $_SESSION['last_activity'] = time();
}

// Error logging
function logError(string $message, array $context = []): void {
    $logMessage = sprintf(
        "[%s] %s%s",
        date('Y-m-d H:i:s'),
        $message,
        !empty($context) ? " - Context: " . json_encode($context, JSON_THROW_ON_ERROR) : ""
    ) . PHP_EOL;
    
    $logFile = __DIR__ . '/error.log';
    error_log($logMessage, 3, $logFile);
} 