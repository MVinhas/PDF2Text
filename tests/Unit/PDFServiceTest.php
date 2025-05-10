<?php

namespace PDF2Text\Tests\Unit;

use PDF2Text\Tests\TestCase;
use PDF2Text\Core\PDFService;
use PDF2Text\Core\Config;

class PDFServiceTest extends TestCase
{
    private PDFService $pdfService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdfService = new PDFService();
    }

    public function testSetLineLength(): void
    {
        // Test default line length
        $this->assertEquals(80, $this->getPrivateProperty($this->pdfService, 'lineLength'));

        // Test setting valid line length
        $this->pdfService->setLineLength(100);
        $this->assertEquals(100, $this->getPrivateProperty($this->pdfService, 'lineLength'));

        // Test minimum line length
        $this->pdfService->setLineLength(30);
        $this->assertEquals(40, $this->getPrivateProperty($this->pdfService, 'lineLength'));

        // Test maximum line length
        $this->pdfService->setLineLength(150);
        $this->assertEquals(120, $this->getPrivateProperty($this->pdfService, 'lineLength'));
    }

    public function testCleanText(): void
    {
        $text = "Hello  World!\tTest\r\n  Multiple   Spaces";
        $expected = "Hello World! Test Multiple Spaces";
        
        $result = $this->invokePrivateMethod($this->pdfService, 'cleanText', [$text]);
        $this->assertEquals($expected, $result);
    }

    public function testLimitText(): void
    {
        $text = "This is a test. This is another sentence. This is the last one.";
        
        // Test with no limit
        $result = $this->invokePrivateMethod($this->pdfService, 'limitText', [$text, 0]);
        $this->assertEquals($text, $result);

        // Test with limit that cuts in the middle of a sentence
        $result = $this->invokePrivateMethod($this->pdfService, 'limitText', [$text, 20]);
        $this->assertEquals("This is a test.", $result);

        // Test with limit that includes full sentences
        $result = $this->invokePrivateMethod($this->pdfService, 'limitText', [$text, 45]);
        $this->assertEquals("This is a test. This is another sentence.", $result);
    }

    public function testWrapText(): void
    {
        $text = "This is a long text that needs to be wrapped into multiple lines. It should respect the line length limit and maintain proper formatting.";
        $this->pdfService->setLineLength(40);

        $result = $this->invokePrivateMethod($this->pdfService, 'wrapText', [$text]);
        
        // Verify that no line exceeds the line length
        $lines = explode("\n", $result);
        foreach ($lines as $line) {
            $this->assertLessThanOrEqual(40, strlen($line));
        }

        // Verify that words are not broken
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            foreach ($words as $word) {
                $this->assertNotEmpty($word);
            }
        }
    }

    public function testCreateParagraphs(): void
    {
        $text = "First sentence. Second sentence. Third sentence. Fourth sentence.";
        
        // Test with no paragraph size limit
        $result = $this->pdfService->createParagraphs($text, 0);
        $this->assertEquals($text, $result);

        // Test with paragraph size limit
        $result = $this->pdfService->createParagraphs($text, 30);
        $paragraphs = explode("\n\n", $result);
        $this->assertCount(4, $paragraphs);
        
        // Verify paragraph lengths
        foreach ($paragraphs as $paragraph) {
            $this->assertLessThanOrEqual(30, strlen($paragraph));
        }
    }

    private function getPrivateProperty($object, string $property)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    private function invokePrivateMethod($object, string $method, array $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
} 