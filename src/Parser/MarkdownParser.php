<?php

declare(strict_types=1);

namespace MarkdownToTei\Parser;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use MarkdownToTei\Config\ConversionConfig;

/**
 * Parser for Markdown text with extended features
 */
class MarkdownParser
{
    private CommonMarkConverter $converter;
    private ConversionConfig $config;

    public function __construct(ConversionConfig $config)
    {
        $this->config = $config;
        $this->initializeConverter();
    }

    /**
     * Initializes the CommonMark converter with extensions
     */
    private function initializeConverter(): void
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // Add core extensions
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new StrikethroughExtension());

        $this->converter = new CommonMarkConverter([], $environment);
    }

    /**
     * Parses Markdown text to HTML
     *
     * @param string $markdown The Markdown text to parse
     * @return string The resulting HTML
     */
    public function parse(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }

    /**
     * Cleans HTML output for better TEI conversion
     *
     * @param string $html The HTML to clean
     * @return string The cleaned HTML
     */
    public function cleanHtml(string $html): string
    {
        // Remove empty paragraphs
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);
        
        // Normalize whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        
        // Remove excessive whitespace at beginning and end
        $html = trim($html);
        
        return $html;
    }
}
