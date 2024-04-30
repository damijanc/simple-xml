<?php

namespace Tests\fixtures;

use damijanc\SimpleXml\Attribute\CData;
use damijanc\SimpleXml\Attribute\Comment;
use damijanc\SimpleXml\Attribute\Node;
use damijanc\SimpleXml\Attribute\Property;
use damijanc\SimpleXml\Attribute\Text;

#[Node('book')]
class Book
{

    #[Property('name', null)]
    #[Property('data-link', 'some-dummy-link')]
    public string $author;

    #[Property('genre', null)]
    public string $genre;

    #[Node('title')]
    #[CData()]
    public string $title;

    #[Node('price')]
    #[Comment('Better be cheap enough!')]
    public string $price;
}
