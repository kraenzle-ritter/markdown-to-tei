<?php

require_once '../vendor/autoload.php';

use MarkdownToTei\Converter;
use MarkdownToTei\Config\ConversionConfig;

// Beispiel für die Verarbeitung einer Datei
$config = new ConversionConfig();
$config->setTeiSetting('title', 'Datei-basierte Konvertierung');
$config->setTeiSetting('author', 'Automatisiertes System');

$converter = new Converter($config);

// Beispiel-Markdown-Datei erstellen
$sampleMarkdown = '# Beispieldokument

Dies ist ein **Beispiel** für die dateibasierte Konvertierung.

## Inhalt

- [Ergänzter Text] in einer Liste
- {Unklare Passage} die markiert wurde
- (Editorielle Anmerkung) für Kontext

### Zitat

> "Dies ist ein wichtiges Zitat mit [ergänztem Text] darin."

Weitere Informationen finden sich --hier-- ++dort++.
';

try {
    // Eingabedatei erstellen
    $inputFile = 'sample_input.md';
    $outputFile = 'sample_output.xml';
    
    file_put_contents($inputFile, $sampleMarkdown);
    echo "Eingabedatei '$inputFile' erstellt.\n\n";
    
    // Datei konvertieren
    $result = $converter->convertFile($inputFile, $outputFile);
    
    echo "=== DATEI KONVERTIERT ===\n";
    echo "Input: $inputFile\n";
    echo "Output: $outputFile\n\n";
    
    echo "=== INHALT DER AUSGABEDATEI ===\n";
    echo file_get_contents($outputFile);
    
    echo "\n\n=== DATEIEN BEREINIGEN ===\n";
    unlink($inputFile);
    unlink($outputFile);
    echo "Temporäre Dateien entfernt.\n";
    
} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}
