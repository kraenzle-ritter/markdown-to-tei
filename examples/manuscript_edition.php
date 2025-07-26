<?php

require_once '../vendor/autoload.php';

use MarkdownToTei\Converter;
use MarkdownToTei\Config\ConversionConfig;

// Demonstration der Flexibilität des Systems
$config = new ConversionConfig();

// TEI-Metadaten konfigurieren
$config->setTeiSetting('title', 'Kritische Edition - Manuskript XYZ');
$config->setTeiSetting('author', 'Johann Wolfgang von Goethe');
$config->setTeiSetting('language', 'de');

// Zusätzliche Konventionen für Manuskript-Editionen
$config->addConvention('page_number', [
    'pattern' => '/\|p\.(\d+)\|/',
    'replacement' => '<pb n="$1"/>',
    'type' => 'regex'
]);

$config->addConvention('line_number', [
    'pattern' => '/\|l\.(\d+)\|/',
    'replacement' => '<lb n="$1"/>',
    'type' => 'regex'
]);

$config->addConvention('gap_damage', [
    'pattern' => '/\[\.\.\.\]/',
    'replacement' => '<gap reason="damage"/>',
    'type' => 'regex'
]);

$config->addConvention('gap_illegible', [
    'pattern' => '/\[\?\?\?\]/',
    'replacement' => '<gap reason="illegible"/>',
    'type' => 'regex'
]);

$config->addConvention('foreign_word', [
    'pattern' => '/\*([a-zA-Z\s]+)\*/',
    'replacement' => '<foreign>$1</foreign>',
    'type' => 'regex'
]);

$config->addConvention('person_name', [
    'pattern' => '/@([A-Z][a-z]+\s[A-Z][a-z]+)@/',
    'replacement' => '<persName>$1</persName>',
    'type' => 'regex'
]);

$config->addConvention('place_name', [
    'pattern' => '/#([A-Z][a-z]+)#/',
    'replacement' => '<placeName>$1</placeName>',
    'type' => 'regex'
]);

// Manuskript-Text mit verschiedenen Editionen
$manuscript = '# Faust I - Fragment

|p.1| **Erster Akt**

|l.1| Habe nun, ach! [Philosophie,] |l.2| Juristerei und {Medizin,} |l.3| Und leider auch Theologie |l.4| Durchaus studiert, mit heißem Bemühn.

(Anmerkung: Hier beginnt das berühmte Gelehrten-Monolog)

|l.5| Da steh ich nun, ich armer Tor! |l.6| Und bin so [klug] als wie zuvor;

## Varianten und Anmerkungen

### Zeile 1-2
- "Philosophie" ist im Original --undeutlich-- geschrieben
- "Medizin" wurde vom Autor {später korrigiert}

### Personen und Orte
@Johann Wolfgang Goethe@ schrieb dieses Werk in #Weimar#.

Das Werk enthält *lateinische* Phrasen und [...] einige unleserliche Stellen.

|p.2| **Zweiter Akt**

Hier sind weitere Textpassagen mit [???] unklaren Bereichen.

Der Text wurde teilweise --gelöscht-- und durch ++neue Formulierungen++ ersetzt.
';

try {
    $converter = new Converter($config);
    $teiXml = $converter->convert($manuscript);
    
    echo "=== MANUSKRIPT-EDITION BEISPIEL ===\n\n";
    echo "=== EINGABE (Markdown mit erweiterten Konventionen) ===\n";
    echo $manuscript . "\n\n";
    
    echo "=== TEI-XML AUSGABE ===\n";
    // Formatiere XML für bessere Lesbarkeit
    $dom = new DOMDocument();
    $dom->loadXML($teiXml);
    $dom->formatOutput = true;
    echo $dom->saveXML();
    
    // Speichere das Beispiel
    file_put_contents('manuscript_edition.xml', $dom->saveXML());
    echo "\n=== AUSGABE GESPEICHERT ===\n";
    echo "Die TEI-XML Ausgabe wurde in 'manuscript_edition.xml' gespeichert.\n";
    
    // Validierung
    echo "\n=== VALIDIERUNG ===\n";
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('tei', 'http://www.tei-c.org/ns/1.0');
    
    $supplied = $xpath->query('//tei:supplied');
    $unclear = $xpath->query('//tei:unclear');
    $pageBreaks = $xpath->query('//tei:pb');
    $lineBreaks = $xpath->query('//tei:lb');
    $persNames = $xpath->query('//tei:persName');
    $placeNames = $xpath->query('//tei:placeName');
    
    echo "✓ Gefunden: " . $supplied->length . " <supplied> Elemente\n";
    echo "✓ Gefunden: " . $unclear->length . " <unclear> Elemente\n";
    echo "✓ Gefunden: " . $pageBreaks->length . " <pb> Seitenumbrüche\n";
    echo "✓ Gefunden: " . $lineBreaks->length . " <lb> Zeilennummern\n";
    echo "✓ Gefunden: " . $persNames->length . " <persName> Personennamen\n";
    echo "✓ Gefunden: " . $placeNames->length . " <placeName> Ortsnamen\n";
    
} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}
