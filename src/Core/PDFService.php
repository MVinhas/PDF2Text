<?php

namespace PDF2Text\Core;

use Smalot\PdfParser\Parser;
use PDF2Text\Security\SecurityService;

class PDFService
{
    private Parser $parser;
    private SecurityService $security;
    private Config $config;
    private int $lineLength = 80; // Default line length for wrapping

    public function __construct()
    {
        $this->parser = new Parser();
        $this->security = new SecurityService();
        $this->config = Config::getInstance();
    }

    public function setLineLength(int $length): void
    {
        $this->lineLength = max(40, min($length, 120)); // Keep between 40 and 120 characters
    }

    public function convertToText(string $filePath, int $maxChars = 0): string
    {
        try {
            if (!file_exists($filePath)) {
                throw new \RuntimeException("PDF file not found at path: " . $filePath);
            }

            // Try using pdftotext first
            $text = $this->convertUsingPdftotext($filePath);
            
            // If pdftotext fails, fall back to PDF Parser
            if (empty(trim($text))) {
                $text = $this->convertUsingParser($filePath);
            }

            $text = $this->cleanText($text);

            // Return empty string if no text content could be extracted
            if (empty($text)) {
                return '';
            }

            $text = $this->limitText($text, $maxChars);
            return $this->wrapText($text);
        } catch (\Throwable $e) {
            throw new \RuntimeException("PDF conversion error: " . $e->getMessage(), 0, $e);
        }
    }

    private function convertUsingPdftotext(string $filePath): string
    {
        $escapedPath = escapeshellarg($filePath);
        $options = $this->config->get('pdf.processor_options', '-layout -raw -enc UTF-8');
        $output = shell_exec("pdftotext $options $escapedPath -");
        return $output ?: '';
    }

    private function convertUsingParser(string $filePath): string
    {
        $pdf = $this->parser->parseFile($filePath);
        return $pdf->getText();
    }

    private function cleanText(string $text): string
    {
        // First, normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Replace tabs with spaces
        $text = str_replace("\t", " ", $text);
        
        // Remove non-printable characters
        $text = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $text);
        
        // Remove any HTML tags
        $text = strip_tags($text);
        
        // Ensure proper spacing after punctuation
        $text = preg_replace('/([.!?])\s*/', '$1 ', $text);
        
        // Normalize spaces - this is the key change
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    private function limitText(string $text, int $maxChars): string
    {
        if ($maxChars === 0 || strlen($text) <= $maxChars) {
            return $text;
        }
    
        $pattern = '/.*?[.!?](?:\s+|$)/'; // Match each sentence including punctuation and following space
        preg_match_all($pattern, $text, $matches);
    
        $result = '';
        $length = 0;
    
        foreach ($matches[0] as $sentence) {
            $sentenceLength = strlen($sentence);
            if ($length + $sentenceLength > $maxChars) {
                break;
            }
            $result .= $sentence;
            $length += $sentenceLength;
        }
    
        return rtrim($result);
    }
    


    private function wrapText(string $text): string
    {
        $lines = [];
        $paragraphs = explode("\n\n", $text);

        foreach ($paragraphs as $paragraph) {
            $words = explode(' ', trim($paragraph));
            $currentLine = '';
            $paragraphLines = [];

            foreach ($words as $word) {
                if (strlen($currentLine . ' ' . $word) <= $this->lineLength) {
                    $currentLine .= ($currentLine === '' ? '' : ' ') . $word;
                } else {
                    if ($currentLine !== '') {
                        $paragraphLines[] = $currentLine;
                    }
                    $currentLine = $word;
                }
            }

            if ($currentLine !== '') {
                $paragraphLines[] = $currentLine;
            }

            $lines[] = implode("\n", $paragraphLines);
        }

        return implode("\n\n", $lines);
    }

    public function createParagraphs(string $text, int $paragraphSize): string
    {
        if ($paragraphSize === 0) {
            return $text;
        }

        // Split into sentences (with punctuation preserved)
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $paragraphs = [];
        $currentParagraph = '';

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            $combined = $currentParagraph === '' ? $sentence : "$currentParagraph $sentence";

            if (strlen($combined) <= $paragraphSize) {
                $currentParagraph = $combined;
            } else {
                if ($currentParagraph !== '') {
                    $paragraphs[] = $currentParagraph;
                }

                // Sentence alone is too long — split it word by word
                if (strlen($sentence) > $paragraphSize) {
                    $words = explode(' ', $sentence);
                    $chunk = '';

                    foreach ($words as $word) {
                        $tentative = $chunk === '' ? $word : "$chunk $word";
                        if (strlen($tentative) <= $paragraphSize) {
                            $chunk = $tentative;
                        } else {
                            if ($chunk !== '') {
                                $paragraphs[] = $chunk;
                            }
                            $chunk = $word;
                        }
                    }

                    if ($chunk !== '') {
                        $paragraphs[] = $chunk;
                    }

                    $currentParagraph = '';
                } else {
                    $currentParagraph = $sentence;
                }
            }
        }

        if ($currentParagraph !== '') {
            $paragraphs[] = $currentParagraph;
        }

        return implode("\n\n", $paragraphs);
    }

} 