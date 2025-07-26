<?php

declare(strict_types=1);

namespace MarkdownToTei;

use MarkdownToTei\Config\ConversionConfig;
use MarkdownToTei\Parser\MarkdownParser;
use MarkdownToTei\Transformer\TeiTransformer;
use MarkdownToTei\Convention\ConventionProcessor;

/**
 * Main class for converting Markdown to TEI-XML
 */
class Converter
{
    private ConversionConfig $config;
    private MarkdownParser $parser;
    private TeiTransformer $transformer;
    private ConventionProcessor $conventionProcessor;

    public function __construct(ConversionConfig $config)
    {
        $this->config = $config;
        $this->parser = new MarkdownParser($config);
        $this->transformer = new TeiTransformer($config);
        $this->conventionProcessor = new ConventionProcessor($config);
    }

    /**
     * Converts Markdown text to TEI-XML
     *
     * @param  string $markdown The Markdown text to convert
     * @return string The resulting TEI-XML
     */
    public function convert(string $markdown): string
    {
        // 1. Apply extended conventions before parsing
        $processedMarkdown = $this->conventionProcessor->preProcess($markdown);

        // 2. Parse Markdown to HTML
        $html = $this->parser->parse($processedMarkdown);

        // 3. Transform HTML to TEI-XML
        $teiXml = $this->transformer->transform($html);

        // 4. Post-processing for additional conventions
        $finalTeiXml = $this->conventionProcessor->postProcess($teiXml);

        return $finalTeiXml;
    }

    /**
     * Converts a Markdown file to TEI-XML
     *
     * @param  string      $inputFile  Path to the Markdown file
     * @param  string|null $outputFile Path to the output file (optional)
     * @return string The resulting TEI-XML
     */
    public function convertFile(string $inputFile, ?string $outputFile = null): string
    {
        if (!file_exists($inputFile)) {
            throw new \InvalidArgumentException("Input file does not exist: {$inputFile}");
        }

        $markdown = file_get_contents($inputFile);
        $teiXml = $this->convert($markdown);

        if ($outputFile !== null) {
            file_put_contents($outputFile, $teiXml);
        }

        return $teiXml;
    }
}
