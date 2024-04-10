<?php
declare(strict_types=1);

namespace damijanc\SimpleXml\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Property
{
    public string $key;
    public ?string $value;

    public function __construct(string $key, ?string $value) {
        $this->key = $key;
        $this->value = $value;
    }
}
