<?php

namespace Asko\Toretto\Tests;

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
}