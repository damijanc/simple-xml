<?php

namespace Tests\fixtures\Pimcore;

use damijanc\SimpleXml\Attribute\Node;
use damijanc\SimpleXml\Attribute\Property;
use damijanc\SimpleXml\Attribute\Text;

class PimcoreProduct
{

    #[Node('PIMCORE_ID')]
    #[Text]
    public string $pimcoreId;

    #[Node('SAP_ID')]
    #[Text]
    public string $sapId;

    public PimcoreImages $images;

    #[Node('NAME_AT')]
    #[Text]
    public string $nameAT;

    #[Node('NAME_DE')]
    #[Text]
    public string $nameDE;


    #[Node('MANUFACTURER')]
    #[Property('sapId', null)]
    public string $manufacturer;

    #[Node('PUBLISHED')]
    public int $published;

    public DatasheetAT $datasheetAT;

    public DatasheetDE $datasheetDE;
}
