<?php

namespace Tests\fixtures;

use damijanc\SimpleXml\Attribute\CData;
use damijanc\SimpleXml\Attribute\Node;
use damijanc\SimpleXml\Attribute\Property;

#[Node('book')]
class Book
{
    #[Node('author')]
    #[Property('author', null)]
    public string $author;

    #[Node('title')]
    #[CData()]
    public string $title;

    #[Node('price')]
    public string $price;

}
