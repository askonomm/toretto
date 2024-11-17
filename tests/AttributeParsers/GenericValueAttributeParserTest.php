<?php

namespace Asko\Toretto\Tests\AttributeParsers;

use Asko\Toretto\Toretto;
use PHPUnit\Framework\TestCase;

class GenericValueAttributeParserTest extends TestCase
{
    public function testParse(): void
    {
        $toretto = new Toretto('<div :class="foo"></div>', ['foo' => 'bar']);
        $this->assertEquals("<div class=\"bar\"></div>", $toretto->toHtml());
    }

    public function testInterpolationParse(): void
    {
        $toretto = new Toretto('<div :id="Hello {foo}"></div>', ['foo' => 'bar']);
        $this->assertEquals("<div id=\"Hello bar\"></div>", $toretto->toHtml());
    }

    public function testMultipleInterpolationParse(): void
    {
        $toretto = new Toretto("<div :foo=\"Hello {foo} and {bar}\"></div>", ["foo" => "bar", "bar" => "baz"]);
        $this->assertEquals("<div foo=\"Hello bar and baz\"></div>", $toretto->toHtml());
    }

    public function testMultipleInterpolationWithSameVarParse(): void
    {
        $toretto = new Toretto("<div :foo=\"Hello {foo} and {foo}\"></div>", ["foo" => "bar"]);
        $this->assertEquals("<div foo=\"Hello bar and bar\"></div>", $toretto->toHtml());
    }
}