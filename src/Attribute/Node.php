<?php
declare(strict_types=1);

namespace damijanc\SimpleXml\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Node
{
    public string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }
}
