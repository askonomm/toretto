<?php

namespace Asko\Toretto\Tests\AttributeParsers;

use Asko\Toretto\Toretto;
use PHPUnit\Framework\TestCase;

class InnerHtmlAttributeParser extends TestCase
{
    public function testParse(): void
    {
        $toretto = new Toretto("<div t-inner-html=\"test\"></div>", ["test" => "test"]);
        $this->assertEquals("<div>test</div>", $toretto->toHtml());
    }

    public function testInterpolation(): void
    {
        $toretto = new Toretto("<div t-inner-html=\"hello {who}\"></div>", ["who" => "world"]);
        $this->assertEquals("<div>hello world</div>", $toretto->toHtml());
    }

    public function testHtml(): void
    {
        $toretto = new Toretto("<div t-inner-html=\"<p>hello {who}</p>\"></div>", ['who' => 'world']);
        $this->assertEquals("<div><p>hello world</p></div>", $toretto->toHtml());
    }
}