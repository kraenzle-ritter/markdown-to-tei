# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-07-26

### Added

- Complete Markdown to TEI-XML converter implementation
- Extended conventions support:
  - `[supplied text]` → `<supplied>supplied text</supplied>`
  - `{unclear text}` → `<unclear>unclear text</unclear>`
  - `(editorial note)` → `<note type="editorial">editorial note</note>`
  - `--deleted text--` → `<del>deleted text</del>`
  - `++added text++` → `<add>added text</add>`
- Flexible configuration system (`ConversionConfig`)
- TEI P5 compliant XML output with proper namespaces
- Configurable TEI metadata (title, author, language, etc.)
- Custom HTML to TEI element mappings
- Comprehensive test suite (27 tests with 100% pass rate)
- GitHub Actions CI/CD pipeline
  - Tests on PHP 8.1, 8.2, 8.3
  - Code style checks (PSR12 compliance)
  - Security audits
  - Dependency analysis
- Code coverage reporting via Codecov
- PSR-4 autoloading
- Composer package ready for Packagist

### Dependencies

- PHP 8.1+ support
- league/commonmark ^2.4
- symfony/dom-crawler ^6.3|^7.0
- symfony/css-selector ^6.3|^7.0
- phpunit/phpunit ^10.0|^11.0 (dev)
- squizlabs/php_codesniffer ^3.7 (dev)

### Documentation

- Complete README with usage examples
- API documentation in code
- Example files demonstrating features
- GitHub badges for CI status, downloads, and version

[1.0.0]: https://github.com/kraenzle-ritter/markdown-to-tei/releases/tag/v1.0.0
