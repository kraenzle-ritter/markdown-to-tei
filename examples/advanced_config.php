<?php

require_once '../vendor/autoload.php';

use MarkdownToTei\Converter;
use MarkdownToTei\Config\ConversionConfig;

// Erweiterte Konfiguration mit benutzerdefinierten Konventionen
$config = new ConversionConfig();

// TEI-Einstellungen anpassen
$config->setTeiSetting('title', 'Kritische Edition - Beispieltext');
$config->setTeiSetting('author', 'Digital Humanities Team');
$config->setTeiSetting('language', 'de');

// Zusätzliche Konventionen hinzufügen
$config->addConvention('gap', [
    'pattern' => '/\[\.\.\.\]/',
    'replacement' => '<gap reason="illegible"/>',
    'type' => 'regex'
]);

$config->addConvention('abbreviation', [
    'pattern' => '/\b([A-Z]{2,})\b/',
    'replacement' => '<abbr>$1</abbr>',
    'type' => 'regex'
]);

$config->addConvention('page_break', [
    'pattern' => '/\|\|/',
    'replacement' => '<pb/>',
    'type' => 'regex'
]);

// Angepasste HTML-zu-TEI-Mappings
$config->addMapping('h1', 'head[@type="main"]');
$config->addMapping('h2', 'head[@type="chapter"]');
$config->addMapping('h3', 'head[@type="section"]');
$config->addMapping('blockquote', 'quote[@rend="block"]');

$markdown = '# Kritische Edition

## Kapitel I: Einführung

Dies ist der Beginn des Textes mit [ergänzten Worten] und {unklaren Passagen}.

### Abschnitt 1.1

Hier finden wir eine ABKÜRZUNG im Text sowie weitere [Ergänzungen].

Es gibt auch Stellen wo [...] Text fehlt.

||

> Dies ist ein wichtiges Zitat, das als Block dargestellt werden soll.

### Abschnitt 1.2

Weiterer Text mit --gestrichenen-- und ++hinzugefügten++ Elementen.

(Anmerkung des Herausgebers: Dies ist wichtig zu beachten)

**Wichtiger Hinweis:** Der Text endet hier.
';

try {
    $converter = new Converter($config);
    $teiXml = $converter->convert($markdown);
    
    echo "=== ERWEITERTE KONFIGURATION BEISPIEL ===\n\n";
    echo "=== MARKDOWN INPUT ===\n";
    echo $markdown . "\n\n";
    
    echo "=== TEI-XML OUTPUT ===\n";
    echo $teiXml . "\n";
    
    // Zusätzliche Validierung
    $dom = new DOMDocument();
    if ($dom->loadXML($teiXml)) {
        echo "\n✓ TEI-XML ist wohlgeformt\n";
    } else {
        echo "\n✗ TEI-XML Formatierungsfehler\n";
    }
    
} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}
