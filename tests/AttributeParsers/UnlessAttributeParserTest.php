<?php

namespace Asko\Toretto\Tests\AttributeParsers;

use Asko\Toretto\Toretto;
use PHPUnit\Framework\TestCase;

class UnlessAttributeParserTest extends TestCase
{
    public function testParse(): void
    {
        $toretto = new Toretto("<div unless=\"test\"></div>", ["test" => "test"]);
        $this->assertEquals("", $toretto->toHtml());
    }

    public function testParseFalse(): void
    {
        $toretto = new Toretto("<div unless=\"test\">hello</div>", ["test" => false]);
        $this->assertEquals("<div>hello</div>", $toretto->toHtml());
    }

    public function testParseCollection(): void
    {
        $toretto = new Toretto("<div unless=\"test\"></div>", ["test" => ["test"]]);
        $this->assertEquals("", $toretto->toHtml());
    }

    public function testParseEmptyCollection(): void
    {
        $toretto = new Toretto("<div unless=\"test\">hello</div>", ["test" => []]);
        $this->assertEquals("<div>hello</div>", $toretto->toHtml());
    }
}