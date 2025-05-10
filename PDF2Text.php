<?php

require_once __DIR__ . '/vendor/autoload.php';

use PDF2Text\Core\Config;
use PDF2Text\Core\PDFService;
use PDF2Text\Security\SecurityService;
use PDF2Text\Utils\Logger;

// Initialize services
$config = Config::getInstance();
$security = new SecurityService();
$pdfService = new PDFService();

// Set up security
$security->secureSession();
$security->setSecurityHeaders();

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Skip CSRF validation in test environment
    if (!$config->isTestEnvironment()) {
        if (!isset($_POST['csrf_token']) || !$security->validateCsrfToken('pdf_upload', $_POST['csrf_token'])) {
            Logger::error('CSRF token validation failed');
            include 'error.php';
            exit(1);
        }
    }

    // Check rate limit
    if (!$security->checkRateLimit($_SERVER['REMOTE_ADDR'])) {
        Logger::warning('Rate limit exceeded', ['ip' => $_SERVER['REMOTE_ADDR']]);
        $_SESSION['last_error'] = 'Too many requests. Please try again later.';
        include 'error.php';
        exit(1);
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    try {
        $file = $_FILES['file'];
        
        // Validate file
        if (!$security->validateFile($file)) {
            throw new RuntimeException('Invalid file upload');
        }

        // Validate input parameters
        $maxChars = filter_var($_POST['max_chars'] ?? 0, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => $config->get('upload.max_allowed_chars', 1000000)]
        ]);

        $paragraphSize = filter_var($_POST['paragraph_size'] ?? 500, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => $config->get('upload.max_allowed_chars', 1000000)]
        ]);

        $lineLength = filter_var($_POST['line_length'] ?? 80, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => $config->get('upload.max_allowed_chars', 1000000)]
        ]);

        if ($maxChars === false || $paragraphSize === false || $lineLength === false) {
            throw new RuntimeException('Invalid input parameters');
        }

        // Process the PDF
        $text = $pdfService->convertToText($file['tmp_name'], $maxChars);
        
        // If no text was extracted, show a message but don't treat it as an error
        if (empty($text)) {
            $_SESSION['converted_text'] = 'No text content could be extracted from the PDF.';
        } else {
            $text = $pdfService->createParagraphs($text, $paragraphSize);
            $_SESSION['converted_text'] = $security->sanitizeOutput($text);
        }
        
        $_SESSION['max_chars'] = $maxChars;
        $_SESSION['paragraph_size'] = $paragraphSize;
        $_SESSION['line_length'] = $lineLength;

        // Clean up
        if (file_exists($file['tmp_name'])) {
            unlink($file['tmp_name']);
        }

        // Redirect to result page
        if (!$config->isTestEnvironment()) {
            include 'result.php';
            exit(0);
        }
    } catch (Throwable $e) {
        Logger::error('PDF processing error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file' => $file['name'] ?? 'unknown'
        ]);
        $_SESSION['last_error'] = sprintf('Error processing PDF: %s', $e->getMessage());
        include 'error.php';
        exit(1);
    }
} else {
    if (!$config->isTestEnvironment()) {
        include 'error.php';
        exit(1);
    }
}
