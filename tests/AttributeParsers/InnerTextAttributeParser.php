<?php

namespace Asko\Toretto\Tests\AttributeParsers;

use Asko\Toretto\Toretto;
use PHPUnit\Framework\TestCase;

class InnerTextAttributeParser extends TestCase
{
    public function testParse(): void
    {
        $toretto = new Toretto("<div t-inner-text=\"test\"></div>", ["test" => "test"]);
        $this->assertEquals("<div>test</div>", $toretto->toHtml());
    }

    public function testInterpolation(): void
    {
        $toretto = new Toretto("<div t-inner-text=\"hello {who}\"></div>", ["who" => "world"]);
        $this->assertEquals("<div>hello world</div>", $toretto->toHtml());
    }
}