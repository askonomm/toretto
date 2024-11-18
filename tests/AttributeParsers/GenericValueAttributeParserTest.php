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

    public function testInterpolationWithModifiersParse(): void
    {
        $toretto = new Toretto("<div :foo=\"Hello {foo | truncate:1}\"></div>", ["foo" => "bar"]);
        $this->assertEquals("<div foo=\"Hello b...\"></div>", $toretto->toHtml());
    }

    public function testInterpolationWithModifiersOnlyParse(): void
    {
        $toretto = new Toretto("<div :foo=\"foo | truncate:1\"></div>", ["foo" => "bar"]);
        $this->assertEquals("<div foo=\"b...\"></div>", $toretto->toHtml());
    }
}