<?php

namespace Tests\fixtures\Pimcore;

use damijanc\SimpleXml\Attribute\Node;
use damijanc\SimpleXml\Attribute\Property;

#[Node('ASSET')]
class PimcoreAsset
{
    #[Property('title', null)]
    public string $title;

    #[Property('path', null)]
    public string $path;

    #[Property('md5', null)]
    public string $md5;

}
