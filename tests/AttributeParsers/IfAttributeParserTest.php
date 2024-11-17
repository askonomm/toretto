<?php

namespace Asko\Toretto\Tests\AttributeParsers;

use Asko\Toretto\Toretto;
use PHPUnit\Framework\TestCase;

class IfAttributeParserTest extends TestCase
{
    public function testParse(): void
    {
        $toretto = new Toretto("<div t-if=\"test\"></div>", ["test" => "test"]);
        $this->assertEquals("<div></div>", $toretto->toHtml());
    }

    public function testParseFalse(): void
    {
        $toretto = new Toretto("<div t-if=\"test\"></div>", ["test" => false]);
        $this->assertEquals("", $toretto->toHtml());
    }

    public function testParseCollection(): void
    {
        $toretto = new Toretto("<div t-if=\"test\"></div>", ["test" => ["test"]]);
        $this->assertEquals("<div></div>", $toretto->toHtml());
    }

    public function testParseEmptyCollection(): void
    {
        $toretto = new Toretto("<div t-if=\"test\"></div>", ["test" => []]);
        $this->assertEquals("", $toretto->toHtml());
    }
}