<?php

declare(strict_types=1);

namespace MarkdownToTei\Transformer;

use DOMDocument;
use DOMElement;
use DOMNode;
use MarkdownToTei\Config\ConversionConfig;

/**
 * Transforms HTML to TEI-XML
 */
class TeiTransformer
{
    private ConversionConfig $config;
    private DOMDocument $teiDoc;

    public function __construct(ConversionConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Transforms HTML to TEI-XML
     *
     * @param string $html The HTML to transform
     * @return string The resulting TEI-XML
     */
    public function transform(string $html): string
    {
        // Clean HTML for better DOM processing
        $html = $this->wrapHtmlForParsing($html);
        
        // HTML-Dokument erstellen mit besserer Fehlerbehandlung
        $htmlDoc = new DOMDocument();
        libxml_use_internal_errors(true);
        $htmlDoc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Create TEI document
        $this->initializeTeiDocument();

        // Body element for content
        $body = $this->teiDoc->createElement('body');
        $text = $this->teiDoc->getElementsByTagName('text')->item(0);
        $text->appendChild($body);

        // Transform HTML elements to TEI
        $bodyElement = $htmlDoc->getElementsByTagName('body')->item(0);
        if ($bodyElement) {
            foreach ($bodyElement->childNodes as $node) {
                $teiNode = $this->transformNode($node);
                if ($teiNode) {
                    $body->appendChild($teiNode);
                }
            }
        } else {
            foreach ($htmlDoc->childNodes as $node) {
                $teiNode = $this->transformNode($node);
                if ($teiNode) {
                    $body->appendChild($teiNode);
                }
            }
        }

        return $this->teiDoc->saveXML();
    }

    /**
     * Initializes the TEI base document
     */
    private function initializeTeiDocument(): void
    {
        $this->teiDoc = new DOMDocument('1.0', 'UTF-8');
        $this->teiDoc->formatOutput = true;

        // TEI-Root-Element
        $tei = $this->teiDoc->createElement('TEI');
        $namespace = $this->config->getTeiSetting('namespace');
        if ($namespace) {
            $tei->setAttribute('xmlns', $namespace);
        }
        $this->teiDoc->appendChild($tei);

        // TEI-Header
        $teiHeader = $this->createTeiHeader();
        $tei->appendChild($teiHeader);

        // Text element
        $text = $this->teiDoc->createElement('text');
        $tei->appendChild($text);
    }

    /**
     * Creates the TEI header
     *
     * @return DOMElement The TEI header
     */
    private function createTeiHeader(): DOMElement
    {
        $teiHeader = $this->teiDoc->createElement('teiHeader');

        // File Description
        $fileDesc = $this->teiDoc->createElement('fileDesc');
        $teiHeader->appendChild($fileDesc);

        // Title Statement
        $titleStmt = $this->teiDoc->createElement('titleStmt');
        $fileDesc->appendChild($titleStmt);

        $title = $this->teiDoc->createElement('title', $this->config->getTeiSetting('title'));
        $titleStmt->appendChild($title);

        $author = $this->config->getTeiSetting('author');
        if ($author) {
            $authorEl = $this->teiDoc->createElement('author', $author);
            $titleStmt->appendChild($authorEl);
        }

        // Publication Statement
        $publicationStmt = $this->teiDoc->createElement('publicationStmt');
        $fileDesc->appendChild($publicationStmt);

        $p = $this->teiDoc->createElement('p', 'Converted from Markdown on ' . $this->config->getTeiSetting('date'));
        $publicationStmt->appendChild($p);

        // Source Description
        $sourceDesc = $this->teiDoc->createElement('sourceDesc');
        $fileDesc->appendChild($sourceDesc);

        $sourceP = $this->teiDoc->createElement('p', 'Born digital');
        $sourceDesc->appendChild($sourceP);

        return $teiHeader;
    }

    /**
     * Transforms an HTML node to a TEI node
     *
     * @param DOMNode $node The node to transform
     * @return DOMNode|null The transformed TEI node
     */
    private function transformNode(DOMNode $node): ?DOMNode
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return $this->teiDoc->createTextNode($node->textContent);
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return null;
        }

        $htmlTag = strtolower($node->nodeName);
        
        // Special handling for already converted TEI elements
        if (in_array($htmlTag, ['supplied', 'unclear', 'del', 'add', 'note'])) {
            return $this->handleTeiElement($node);
        }
        
        $teiMapping = $this->config->getMapping($htmlTag);

        if (!$teiMapping) {
            // Fallback: div for unknown elements
            $teiMapping = 'div[@type="' . $htmlTag . '"]';
        }

        // Create TEI element
        $teiElement = $this->createTeiElement($teiMapping);

        // Copy attributes (if necessary)
        $this->copyRelevantAttributes($node, $teiElement);

        // Transform child nodes
        foreach ($node->childNodes as $childNode) {
            $teiChild = $this->transformNode($childNode);
            if ($teiChild) {
                $teiElement->appendChild($teiChild);
            }
        }

        return $teiElement;
    }

    /**
     * Creates a TEI element based on the mapping
     *
     * @param string $mapping The element mapping (e.g. "head[@type='main']")
     * @return DOMElement The created TEI element
     */
    private function createTeiElement(string $mapping): DOMElement
    {
        // Parse the mapping (Element[@attribute='value'])
        if (preg_match('/^([a-zA-Z]+)(?:\[@([a-zA-Z]+)=["\']([^"\']+)["\']\])?$/', $mapping, $matches)) {
            $elementName = $matches[1];
            $element = $this->teiDoc->createElement($elementName);

            if (isset($matches[2]) && isset($matches[3])) {
                $element->setAttribute($matches[2], $matches[3]);
            }

            return $element;
        }

        // Fallback: simple element
        return $this->teiDoc->createElement($mapping);
    }

    /**
     * Copies relevant attributes from HTML to TEI element
     *
     * @param DOMNode $htmlNode The HTML node
     * @param DOMElement $teiElement The TEI element
     */
    private function copyRelevantAttributes(DOMNode $htmlNode, DOMElement $teiElement): void
    {
        if (!$htmlNode->hasAttributes()) {
            return;
        }

        foreach ($htmlNode->attributes as $attribute) {
            $name = $attribute->name;
            $value = $attribute->value;

            // Only copy certain attributes
            if (in_array($name, ['id', 'class', 'lang'])) {
                if ($name === 'class') {
                    $teiElement->setAttribute('rend', $value);
                } else {
                    $teiElement->setAttribute($name, $value);
                }
            }
        }
    }
    
    /**
     * Prepares HTML for better DOM parsing
     *
     * @param string $html The HTML
     * @return string The prepared HTML
     */
    private function wrapHtmlForParsing(string $html): string
    {
        // Add UTF-8 meta tag for correct encoding
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>';
        return $html;
    }
    
    /**
     * Handles already converted TEI elements
     *
     * @param DOMNode $node The TEI node
     * @return DOMElement The TEI element
     */
    private function handleTeiElement(DOMNode $node): DOMElement
    {
        $tagName = $node->nodeName;
        $element = $this->teiDoc->createElement($tagName);
        
        // Copy attributes
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attribute) {
                $element->setAttribute($attribute->name, $attribute->value);
            }
        }
        
        // Copy text content
        $element->textContent = $node->textContent;
        
        return $element;
    }
}
