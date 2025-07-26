<?php

declare(strict_types=1);

namespace MarkdownToTei\Tests\Config;

use PHPUnit\Framework\TestCase;
use MarkdownToTei\Config\ConversionConfig;

class ConversionConfigTest extends TestCase
{
    private ConversionConfig $config;

    protected function setUp(): void
    {
        $this->config = new ConversionConfig();
    }

    public function testDefaultConventions(): void
    {
        $conventions = $this->config->getConventions();

        $this->assertArrayHasKey('supplied', $conventions);
        $this->assertArrayHasKey('unclear', $conventions);
        $this->assertArrayHasKey('editorial_note', $conventions);
        $this->assertArrayHasKey('deletion', $conventions);
        $this->assertArrayHasKey('addition', $conventions);
    }

    public function testAddConvention(): void
    {
        $this->config->addConvention(
            'test',
            [
            'pattern' => '/test/',
            'replacement' => '<test>$1</test>',
            'type' => 'regex'
            ]
        );

        $conventions = $this->config->getConventions();
        $this->assertArrayHasKey('test', $conventions);
        $this->assertEquals('/test/', $conventions['test']['pattern']);
    }

    public function testRemoveConvention(): void
    {
        $this->config->removeConvention('supplied');
        $conventions = $this->config->getConventions();

        $this->assertArrayNotHasKey('supplied', $conventions);
    }

    public function testTeiSettings(): void
    {
        $this->config->setTeiSetting('custom', 'value');
        $this->assertEquals('value', $this->config->getTeiSetting('custom'));
        $this->assertNull($this->config->getTeiSetting('nonexistent'));
        $this->assertEquals('default', $this->config->getTeiSetting('nonexistent', 'default'));
    }

    public function testDefaultTeiSettings(): void
    {
        $this->assertEquals('http://www.tei-c.org/ns/1.0', $this->config->getTeiSetting('namespace'));
        $this->assertEquals('UTF-8', $this->config->getTeiSetting('encoding'));
        $this->assertEquals('Converted from Markdown', $this->config->getTeiSetting('title'));
    }

    public function testMappings(): void
    {
        $this->config->addMapping('custom', 'customElement');
        $this->assertEquals('customElement', $this->config->getMapping('custom'));
        $this->assertNull($this->config->getMapping('nonexistent'));

        $mappings = $this->config->getMappings();
        $this->assertArrayHasKey('custom', $mappings);
    }

    public function testDefaultMappings(): void
    {
        $this->assertEquals('head[@type="main"]', $this->config->getMapping('h1'));
        $this->assertEquals('p', $this->config->getMapping('p'));
        $this->assertEquals('hi[@rend="bold"]', $this->config->getMapping('strong'));
    }
}
