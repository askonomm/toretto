<?php

namespace Asko\Toretto\Tests\ExpressionModifiers;

use Asko\Toretto\Toretto;
use PHPUnit\Framework\TestCase;

class TruncateExpressionModifierTest extends TestCase
{
    public function testTruncateString(): void
    {
        $toretto = new Toretto('<span inner-text="name | truncate: 10"></span>', ['name' => 'John LongName Peter The Third']);
        $this->assertEquals('<span>John Lo...</span>', $toretto->toHtml());
    }

    public function testTruncateStringNoOpts(): void
    {
        $toretto = new Toretto('<span inner-text="name | truncate"></span>', ['name' => 'John LongName Peter The Third']);
        $this->assertEquals('<span>John LongName Peter The Third</span>', $toretto->toHtml());
    }

    public function testTruncateCollection(): void
    {
        $toretto = new Toretto('<span inner-text="names | truncate: 1"></span>', ['names' => ['John LongName Peter The Third', 'John LongName Peter The Fourth']]);
        $this->assertEquals('<span>["John LongName Peter The Third"]</span>', $toretto->toHtml());
    }

    public function testTruncateCollectionNoOpts(): void
    {
        $toretto = new Toretto('<span inner-text="names | truncate"></span>', ['names' => ['John LongName Peter The Third', 'John LongName Peter The Fourth']]);
        $this->assertEquals('<span>["John LongName Peter The Third","John LongName Peter The Fourth"]</span>', $toretto->toHtml());
    }

    public function testTruncateInteger(): void
    {
        $toretto = new Toretto('<span inner-text="age | truncate: 1"></span>', ['age' => 100]);
        $this->assertEquals('<span>100</span>', $toretto->toHtml());
    }
}