<?php

namespace PDF2Text\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDF2Text\Core\Config;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize session for testing
        $_SESSION = [];
        $_POST = [];
        $_FILES = [];
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'HTTP_USER_AGENT' => 'PHPUnit Test',
            'REMOTE_ADDR' => '127.0.0.1'
        ];
        
        // Set test environment
        Config::getInstance()->setTestEnvironment(true);
    }

    protected function tearDown(): void
    {
        // Clean up session data
        $_SESSION = [];
        parent::tearDown();
    }

    protected function createTestPdf(string $text): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'test_pdf_');
    
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, $text);
        $pdf->Output('F', $tempPath);  // Write PDF to file
    
        return $tempPath;
    }    

    protected function createMockFileUpload(string $content): array
    {
        $tempFile = $this->createTestPdf($content);
        return [
            'name' => 'test.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($tempFile)
        ];
    }
} 