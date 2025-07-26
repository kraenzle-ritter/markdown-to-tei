<?php

declare(strict_types=1);

namespace MarkdownToTei\Config;

/**
 * Configuration for Markdown-to-TEI conversion
 */
class ConversionConfig
{
    private array $conventions = [];
    private array $teiSettings = [];
    private array $mappings = [];

    public function __construct()
    {
        $this->initializeDefaults();
    }

    /**
     * Initializes default conventions and settings
     */
    private function initializeDefaults(): void
    {
        // Standard conventions for extended Markdown syntax
        $this->conventions = [
            'supplied' => [
                'pattern' => '/\[([^\]]+)\]/',
                'replacement' => '<supplied>$1</supplied>',
                'type' => 'regex'
            ],
            'unclear' => [
                'pattern' => '/\{([^}]+)\}/',
                'replacement' => '<unclear>$1</unclear>',
                'type' => 'regex'
            ],
            'editorial_note' => [
                'pattern' => '/\(([^)]+)\)/',
                'replacement' => '<note type="editorial">$1</note>',
                'type' => 'regex'
            ],
            'deletion' => [
                'pattern' => '/--([^-]+)--/',
                'replacement' => '<del>$1</del>',
                'type' => 'regex'
            ],
            'addition' => [
                'pattern' => '/\+\+([^+]+)\+\+/',
                'replacement' => '<add>$1</add>',
                'type' => 'regex'
            ]
        ];

        // TEI basic settings
        $this->teiSettings = [
            'namespace' => 'http://www.tei-c.org/ns/1.0',
            'schema' => 'https://www.tei-c.org/release/xml/tei/custom/schema/relaxng/tei_all.rng',
            'encoding' => 'UTF-8',
            'version' => '1.0',
            'title' => 'Converted from Markdown',
            'author' => '',
            'date' => date('Y-m-d'),
            'language' => 'de'
        ];

        // HTML-to-TEI mappings
        $this->mappings = [
            'h1' => 'head[@type="main"]',
            'h2' => 'head[@type="section"]',
            'h3' => 'head[@type="subsection"]',
            'p' => 'p',
            'strong' => 'hi[@rend="bold"]',
            'em' => 'hi[@rend="italic"]',
            'ul' => 'list[@type="unordered"]',
            'ol' => 'list[@type="ordered"]',
            'li' => 'item',
            'blockquote' => 'quote',
            'code' => 'code',
            'pre' => 'ab[@type="code"]'
        ];
    }

    public function addConvention(string $name, array $convention): void
    {
        $this->conventions[$name] = $convention;
    }

    public function removeConvention(string $name): void
    {
        unset($this->conventions[$name]);
    }

    public function getConventions(): array
    {
        return $this->conventions;
    }

    public function setTeiSetting(string $key, $value): void
    {
        $this->teiSettings[$key] = $value;
    }

    public function getTeiSetting(string $key, $default = null)
    {
        return $this->teiSettings[$key] ?? $default;
    }

    public function getTeiSettings(): array
    {
        return $this->teiSettings;
    }

    public function addMapping(string $htmlTag, string $teiElement): void
    {
        $this->mappings[$htmlTag] = $teiElement;
    }

    public function getMappings(): array
    {
        return $this->mappings;
    }

    public function getMapping(string $htmlTag): ?string
    {
        return $this->mappings[$htmlTag] ?? null;
    }
}
