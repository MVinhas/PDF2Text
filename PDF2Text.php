<?php
session_start();
require 'vendor/autoload.php';

function pdfToText(string $filePath, int $maxChars): string {
    $escapedPath = escapeshellarg($filePath);

    $output = shell_exec("pdftotext $escapedPath -");
    $text = strip_tags($output);

    if ($maxChars === 0) return $text;
    
    if (strlen($text) <= $maxChars) {
        return $text;
    }

    $subText = substr($text, 0, $maxChars);

    $lastSentenceEndPos = max(
        strrpos($subText, '.'), 
        strrpos($subText, '?'), 
        strrpos($subText, '!')
    );

    if ($lastSentenceEndPos === false) {
        return $subText;
    }

    return substr($text, 0, $lastSentenceEndPos + 1);
}

function handleFileUpload(array $file, int $maxChars, int $paragraphSize): void {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        handleError();
    }

    $filePath = $file['tmp_name'];
    $text = pdfToText($filePath, $maxChars);

    $text = createParagraphs($text, $paragraphSize);

    include 'result.php';
    unlink($filePath);
}

function handleError(): void {
    include 'error.php';
    exit(1);
}

function createParagraphs(string $text, int $paragraphSize): string {
    $text = preg_replace('/\s+/', ' ', trim($text)); 
    $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $paragraphs = [];
    $currentParagraph = '';

    foreach ($sentences as $sentence) {
        if (strlen($currentParagraph . ' ' . $sentence) <= $paragraphSize) {
            $currentParagraph .= ($currentParagraph === '' ? '' : ' ') . $sentence;
        } else {
            $paragraphs[] = trim($currentParagraph);
            $currentParagraph = $sentence;
        }
    }

    if (!empty($currentParagraph)) {
        $paragraphs[] = trim($currentParagraph);
    }

    return implode("\n\n", $paragraphs);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        handleError();
    }
    $file = $_FILES['file'];
    $maxChars = isset($_POST['max_chars']) ? (int)$_POST['max_chars'] : 0;
    $_SESSION['max_chars'] = $maxChars;
    $paragraphSize = isset($_POST['paragraph_size']) ? (int)$_POST['paragraph_size'] : 500;
    $_SESSION['paragraph_size'] = $paragraphSize;
    handleFileUpload(file: $file, maxChars: $maxChars, paragraphSize: $paragraphSize);
} else {
    handleError();
}
