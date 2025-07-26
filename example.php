<?php

require_once 'vendor/autoload.php';

use MarkdownToTei\Converter;
use MarkdownToTei\Config\ConversionConfig;

// Example Markdown with extended conventions
$markdown = '# Document Title

This is a sample text with various **important** and *emphasized* elements.

## Extended Conventions

Here is [supplied text] in the document.

There are also {unclear passages} that need to be marked.

(This is an editorial note)

This text was --deleted-- and this was ++added++.

### List of Points

- First point
- Second point with [supplied text]
- Third point with {unclear passage}

### Quote

> This is an important quote from the original text.
> It extends over multiple lines.

### Code Example

```php
$example = "This is a code example";
echo $example;
```

## Table

| Column 1 | Column 2 | Column 3 |
|----------|----------|----------|
| Value 1  | [Value 2] | {Value 3} |
| Value 4  | Value 5  | Value 6  |
';

try {
    // Create configuration
    $config = new ConversionConfig();
    
    // Optional: Customize configuration
    $config->setTeiSetting('title', 'Example Document');
    $config->setTeiSetting('author', 'Test Author');
    
    // Create converter
    $converter = new Converter($config);
    
    // Perform conversion
    $teiXml = $converter->convert($markdown);
    
    echo "=== MARKDOWN INPUT ===\n";
    echo $markdown . "\n\n";
    
    echo "=== TEI-XML OUTPUT ===\n";
    echo $teiXml . "\n";
    
    // Optional: Save to file
    file_put_contents('example_output.xml', $teiXml);
    echo "\nOutput saved to 'example_output.xml'.\n";
    
} catch (Exception $e) {
    echo "Error during conversion: " . $e->getMessage() . "\n";
}
