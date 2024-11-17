<?php

namespace Asko\Toretto\Core\Attributes;

#[\Attribute]
class Name
{
    public function __construct(public string $name) {}
}