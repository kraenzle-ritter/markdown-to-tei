<?php

declare(strict_types=1);

namespace MarkdownToTei\Convention;

use MarkdownToTei\Config\ConversionConfig;

/**
 * Processes extended conventions before and after main conversion
 */
class ConventionProcessor
{
    private ConversionConfig $config;

    public function __construct(ConversionConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Applies conventions before Markdown parsing
     *
     * @param  string $markdown The original Markdown text
     * @return string The processed Markdown text
     */
    public function preProcess(string $markdown): string
    {
        // Here we do NOT apply conventions yet,
        // as they should be converted directly to TEI
        return $markdown;
    }

    /**
     * Applies conventions after TEI transformation
     *
     * @param  string $teiXml The TEI-XML
     * @return string The processed TEI-XML
     */
    public function postProcess(string $teiXml): string
    {
        $processed = $teiXml;

        // Apply extended conventions
        $processed = $this->applyConventionsToTei($processed);

        // Standard-Bereinigungen
        $processed = $this->cleanupTeiXml($processed);
        $processed = $this->addTeiNamespaces($processed);

        return $processed;
    }

    /**
     * Applies a regex convention
     *
     * @param  string $text       The text to process
     * @param  array  $convention The convention definition
     * @return string The processed text
     */
    private function applyRegexConvention(string $text, array $convention): string
    {
        $pattern = $convention['pattern'];
        $replacement = $convention['replacement'];

        return preg_replace($pattern, $replacement, $text);
    }

    /**
     * Cleans the TEI-XML
     *
     * @param  string $teiXml The TEI-XML to clean
     * @return string The cleaned TEI-XML
     */
    private function cleanupTeiXml(string $teiXml): string
    {
        // Remove double spaces
        $teiXml = preg_replace('/\s+/', ' ', $teiXml);

        // Normalize line breaks
        $teiXml = str_replace(["\r\n", "\r"], "\n", $teiXml);

        // Remove empty lines
        $teiXml = preg_replace('/\n\s*\n/', "\n", $teiXml);

        return trim($teiXml);
    }

    /**
     * Adds TEI namespaces if necessary
     *
     * @param  string $teiXml The TEI-XML
     * @return string The TEI-XML with namespaces
     */
    private function addTeiNamespaces(string $teiXml): string
    {
        $namespace = $this->config->getTeiSetting('namespace');

        // Check if a namespace is already present
        if (strpos($teiXml, 'xmlns') === false && $namespace) {
            $teiXml = str_replace(
                '<TEI>',
                '<TEI xmlns="' . $namespace . '">',
                $teiXml
            );
        }

        return $teiXml;
    }

    /**
     * Wendet die Konventionen auf das TEI-XML an
     *
     * @param  string $teiXml Das TEI-XML
     * @return string Das verarbeitete TEI-XML
     */
    private function applyConventionsToTei(string $teiXml): string
    {
        $processed = $teiXml;
        $conventions = $this->config->getConventions();

        foreach ($conventions as $name => $convention) {
            if ($convention['type'] === 'regex') {
                $processed = $this->applyRegexConvention($processed, $convention);
            }
        }

        return $processed;
    }

    /**
     * Adds a new convention
     *
     * @param string $name        Name of the convention
     * @param string $pattern     Regex pattern
     * @param string $replacement Replacement text
     * @param string $type        Type of convention (default: 'regex')
     */
    public function addConvention(string $name, string $pattern, string $replacement, string $type = 'regex'): void
    {
        $this->config->addConvention(
            $name,
            [
            'pattern' => $pattern,
            'replacement' => $replacement,
            'type' => $type
            ]
        );
    }
}
