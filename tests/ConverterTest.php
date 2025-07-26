<?php

declare(strict_types=1);

namespace MarkdownToTei\Tests;

use PHPUnit\Framework\TestCase;
use MarkdownToTei\Converter;
use MarkdownToTei\Config\ConversionConfig;

class ConverterTest extends TestCase
{
    private Converter $converter;
    private ConversionConfig $config;

    protected function setUp(): void
    {
        $this->config = new ConversionConfig();
        $this->converter = new Converter($this->config);
    }

    public function testBasicMarkdownConversion(): void
    {
        $markdown = "# Test Title\n\nThis is **bold** and *italic* text.";
        $result = $this->converter->convert($markdown);

        $this->assertStringContainsString('<TEI', $result);
        $this->assertStringContainsString('<head', $result);
        $this->assertStringContainsString('<hi rend="bold">bold</hi>', $result);
        $this->assertStringContainsString('<hi rend="italic">italic</hi>', $result);
    }

    public function testSuppliedTextConvention(): void
    {
        $markdown = "This text has [supplied content] in it.";
        $result = $this->converter->convert($markdown);

        $this->assertStringContainsString('<supplied>supplied content</supplied>', $result);
    }

    public function testUnclearTextConvention(): void
    {
        $markdown = "This text has {unclear content} in it.";
        $result = $this->converter->convert($markdown);

        $this->assertStringContainsString('<unclear>unclear content</unclear>', $result);
    }

    public function testEditorialNoteConvention(): void
    {
        $markdown = "This text has (editorial note) in it.";
        $result = $this->converter->convert($markdown);

        $this->assertStringContainsString('<note type="editorial">editorial note</note>', $result);
    }

    public function testDeletionConvention(): void
    {
        $markdown = "This text has --deleted content-- in it.";
        $result = $this->converter->convert($markdown);

        $this->assertStringContainsString('<del>deleted content</del>', $result);
    }

    public function testAdditionConvention(): void
    {
        $markdown = "This text has ++added content++ in it.";
        $result = $this->converter->convert($markdown);

        $this->assertStringContainsString('<add>added content</add>', $result);
    }

    public function testComplexMarkdown(): void
    {
        $markdown = "# Main Title\n\n## Section\n\nText with [supplied] and {unclear} parts.\n\n" .
                   "- List item with **bold**\n- Another item\n\n> Blockquote text";
        $result = $this->converter->convert($markdown);

        $this->assertStringContainsString('<TEI', $result);
        $this->assertStringContainsString('<supplied>supplied</supplied>', $result);
        $this->assertStringContainsString('<unclear>unclear</unclear>', $result);
        $this->assertStringContainsString('<list', $result);
        $this->assertStringContainsString('<quote', $result);
    }

    public function testTeiStructure(): void
    {
        $markdown = "# Test\n\nContent";
        $result = $this->converter->convert($markdown);

        $dom = new \DOMDocument();
        $this->assertTrue($dom->loadXML($result), 'Generated TEI-XML should be well-formed');

        $this->assertStringContainsString('<teiHeader>', $result);
        $this->assertStringContainsString('<fileDesc>', $result);
        $this->assertStringContainsString('<titleStmt>', $result);
        $this->assertStringContainsString('<text>', $result);
        $this->assertStringContainsString('<body>', $result);
    }

    public function testCustomConfiguration(): void
    {
        $this->config->setTeiSetting('title', 'Custom Title');
        $this->config->setTeiSetting('author', 'Test Author');

        $markdown = "# Test";
        $result = $this->converter->convert($markdown);

        $this->assertStringContainsString('<title>Custom Title</title>', $result);
        $this->assertStringContainsString('<author>Test Author</author>', $result);
    }
}
