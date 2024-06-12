<?php

namespace App\Attributes;

use Attribute;

#[Attribute]
class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET',
        public array $requirements = []
    ) {}
}
