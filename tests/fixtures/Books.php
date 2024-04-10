<?php

namespace Tests\fixtures;

use damijanc\SimpleXml\Attribute\Node;
use damijanc\SimpleXml\Attribute\Property;

#[Node('books')]
#[Property('type', 'fiction')]
class Books
{
    public array $books;
}
