<?php

declare(strict_types=1);

namespace MarkdownToTei\Tests\Convention;

use PHPUnit\Framework\TestCase;
use MarkdownToTei\Convention\ConventionProcessor;
use MarkdownToTei\Config\ConversionConfig;

class ConventionProcessorTest extends TestCase
{
    private ConventionProcessor $processor;
    private ConversionConfig $config;

    protected function setUp(): void
    {
        $this->config = new ConversionConfig();
        $this->processor = new ConventionProcessor($this->config);
    }

    public function testSuppliedTextPreProcessing(): void
    {
        $input = "Text with [supplied content] here.";
        $result = $this->processor->preProcess($input);
        
        // PreProcessing macht jetzt nichts mehr
        $this->assertEquals($input, $result);
    }

    public function testUnclearTextPreProcessing(): void
    {
        $input = "Text with {unclear content} here.";
        $result = $this->processor->preProcess($input);
        
        // PreProcessing macht jetzt nichts mehr
        $this->assertEquals($input, $result);
    }

    public function testEditorialNotePreProcessing(): void
    {
        $input = "Text with (editorial note) here.";
        $result = $this->processor->preProcess($input);
        
        // PreProcessing macht jetzt nichts mehr
        $this->assertEquals($input, $result);
    }

    public function testDeletionPreProcessing(): void
    {
        $input = "Text with --deleted content-- here.";
        $result = $this->processor->preProcess($input);
        
        // PreProcessing macht jetzt nichts mehr
        $this->assertEquals($input, $result);
    }

    public function testAdditionPreProcessing(): void
    {
        $input = "Text with ++added content++ here.";
        $result = $this->processor->preProcess($input);
        
        // PreProcessing macht jetzt nichts mehr
        $this->assertEquals($input, $result);
    }

    public function testMultipleConventions(): void
    {
        $input = "Text with [supplied] and {unclear} and (note) content.";
        $result = $this->processor->preProcess($input);
        
        // PreProcessing macht jetzt nichts mehr
        $this->assertEquals($input, $result);
    }

    public function testPostProcessing(): void
    {
        $input = '<TEI><text><body><p>Test content</p></body></text></TEI>';
        $result = $this->processor->postProcess($input);
        
        // Should add namespace
        $this->assertStringContainsString('xmlns="http://www.tei-c.org/ns/1.0"', $result);
    }

    public function testAddCustomConvention(): void
    {
        $this->processor->addConvention('test', '/TEST/', '<test/>');
        
        // Test im Post-Processing
        $input = "This is a <p>TEST</p>.";
        $result = $this->processor->postProcess($input);
        
        $this->assertStringContainsString('<test/>', $result);
    }

    public function testCleanupExcessiveWhitespace(): void
    {
        $input = '<TEI>  <text>   <body>     <p>Test</p>   </body>  </text>  </TEI>';
        $result = $this->processor->postProcess($input);
        
        $this->assertStringNotContainsString('  ', $result);
    }

    public function testPostProcessingConventions(): void
    {
        $input = '<TEI><text><body><p>Text with [supplied content] here.</p></body></text></TEI>';
        $result = $this->processor->postProcess($input);
        
        $this->assertStringContainsString('<supplied>supplied content</supplied>', $result);
    }

    public function testPostProcessingMultipleConventions(): void
    {
        $input = '<TEI><text><body><p>Text with [supplied] and {unclear} content.</p></body></text></TEI>';
        $result = $this->processor->postProcess($input);
        
        $this->assertStringContainsString('<supplied>supplied</supplied>', $result);
        $this->assertStringContainsString('<unclear>unclear</unclear>', $result);
    }
}
