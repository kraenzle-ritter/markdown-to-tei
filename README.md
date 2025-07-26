# Markdown to TEI Converter

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![CI](https://github.com/kraenzle-ritter/markdown-to-tei/workflows/CI/badge.svg)](https://github.com/kraenzle-ritter/markdown-to-tei/actions)
[![Quality Assurance](https://github.com/kraenzle-ritter/markdown-to-tei/workflows/Quality%20Assurance/badge.svg)](https://github.com/kraenzle-ritter/markdown-to-tei/actions)
[![Downloads](https://img.shields.io/packagist/dt/kraenzle-ritter/markdown-to-tei)](https://packagist.org/packages/kraenzle-ritter/markdown-to-tei)
[![Latest Version](https://img.shields.io/packagist/v/kraenzle-ritter/markdown-to-tei)](https://packagist.org/packages/kraenzle-ritter/markdown-to-tei)
[![PHPUnit Tests](https://img.shields.io/badge/tests-27%20passing-brightgreen.svg)](#testing)
[![TEI P5](https://img.shields.io/badge/TEI-P5%20compliant-orange.svg)](https://tei-c.org/)
[![Composer](https://img.shields.io/badge/composer-ready-blue.svg)](https://getcomposer.org/)

A flexible PHP-based converter that transforms Markdown into TEI-XML with support for extended conventions and customizable mappings.

## Features

- **Markdown to TEI-XML conversion** with full TEI P5 compliance
- **Extended conventions** (e.g., `[]` to `<supplied>`, `{}` to `<unclear>`)
- **Flexible configuration system** for custom rules and mappings
- **Configurable TEI metadata** (title, author, language, etc.)
- **TEI P5 standard compliant** output with proper namespaces
- **High performance** processing of large documents
- **Fully tested** with comprehensive PHPUnit test suite

## Installation

```bash
composer install
```

## Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use MarkdownToTei\Converter;
use MarkdownToTei\Config\ConversionConfig;

$config = new ConversionConfig();
$converter = new Converter($config);

$markdown = "# Title\n\nThis is a [supplied text] with **important** text.";
$teiXml = $converter->convert($markdown);

echo $teiXml;
```

## Extended Conventions

### Supplied Text

```markdown
This is a [supplied text] in the document.
```

becomes:

```xml
This is a <supplied>supplied text</supplied> in the document.
```

### Additional Conventions

- `{unclear text}` → `<unclear>unclear text</unclear>`
- `(editorial note)` → `<note type="editorial">editorial note</note>`
- `--deleted--` → `<del>deleted</del>`
- `++added++` → `<add>added</add>`

## Advanced Configuration

```php
<?php
// Configure TEI metadata
$config->setTeiSetting('title', 'My Document');
$config->setTeiSetting('author', 'Author Name');
$config->setTeiSetting('language', 'en');

// Add custom conventions
$config->addConvention('page_break', [
    'pattern' => '/\|p\.(\d+)\|/',
    'replacement' => '<pb n="$1"/>',
    'type' => 'regex'
]);

// Custom HTML to TEI mappings
$config->addMapping('h1', 'head[@type="chapter"]');
```

## Examples

1. **`example.php`**: Basic functionality demonstration
2. **`examples/advanced_config.php`**: Advanced configuration options
3. **`examples/file_conversion.php`**: File-based conversion
4. **`examples/manuscript_edition.php`**: Critical edition with special conventions

## Testing

Run the test suite:

```bash
composer test
```

**27 tests** with **100% pass rate** ensuring reliability and correctness.

## Requirements

- PHP 8.1 or higher
- Composer for dependency management

## Dependencies

- `league/commonmark` - Robust Markdown parsing
- `symfony/dom-crawler` - Reliable HTML/XML manipulation
- `symfony/css-selector` - CSS selector support

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
