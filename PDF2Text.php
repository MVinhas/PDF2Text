<?php
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

function pdfToText(string $filePath): string {
    $escapedPath = escapeshellarg($filePath);
    $output = shell_exec("PDF2Text $escapedPath -");
    $output = strip_tags($output);
    $output = preg_replace('/\s+/', ' ', $output);
    return $output;
}

function truncateText(string $text, int $length = 1000, string $suffix = ''.PHP_EOL): string {
    if (strlen($text) <= $length) {
        return $text . ' ' . $suffix;
    }
    $truncated = substr($text, 0, $length);
    $lastSpace = strrpos($truncated, '.');
    if ($lastSpace !== false) {
        $truncated = substr($truncated, 0, $lastSpace);
    }
    return $truncated . '. ' . $suffix;
}

if (php_sapi_name() === 'cli') {
    if ($argc !== 2) {
        echo "Usage: php PDF2Text.php <path_to_pdf>\n";
        exit(1);
    }

    $filePath = $argv[1];
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        exit(1);
    }

    $text = pdfToText($filePath);
    $truncatedText = truncateText($text);
    echo $truncatedText;
} else {
    echo "This script must be run from the command line.\n";
}