<?php
namespace damijanc\SimpleXml\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Comment
{
    public function __construct() {}
}
