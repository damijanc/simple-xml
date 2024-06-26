<?php
namespace damijanc\SimpleXml\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Comment
{
    public string $comment;

    public function __construct(string $comment) {
        $this->comment = $comment;
    }
}
