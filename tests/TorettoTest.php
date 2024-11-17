<?php

namespace Asko\Toretto\Tests;

use Asko\Loggr\Drivers\OutputDriver;
use Asko\Loggr\Loggr;
use Asko\Toretto\AttributeParsers\BaseAttributeParser;
use Asko\Toretto\Toretto;
use PHPUnit\Framework\TestCase;

class TorettoTest extends TestCase
{
    public function testDocumentWithDeclaration(): void
    {
        $toretto = new Toretto('<!DOCTYPE html><html><head></head><body></body></html>');
        $this->assertEquals("<!DOCTYPE html><html><head></head><body></body></html>", $toretto->toHtml());
    }

    public function testDocumentWithoutDeclaration(): void
    {
        $toretto = new Toretto('<html><head></head><body></body></html>');
        $this->assertEquals("<html><head></head><body></body></html>", $toretto->toHtml());
    }

    public function testAttributeParserQueryAttributeMissing(): void
    {
        $this->expectOutputRegex("/Query attribute is missing/");
        $logger = new Loggr(new OutputDriver());
        $toretto = new Toretto('');
        $toretto->logger = $logger;
        $toretto->attributeParsers = [new class extends BaseAttributeParser {
            public function parse(\DOM\NodeList &$nodeList): void
            {
            }
        }];

        $html = $toretto->toHtml();
        $this->assertEquals('', $html);
    }

    public function testAttributeThrowsException2(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Attribute parser must be a subclass of BaseAttributeParser.");
        $toretto = new Toretto('<html><head></head><body></body></html>', ['test' => 'test']);
        $toretto->attributeParsers = [new class {}];
    }
}