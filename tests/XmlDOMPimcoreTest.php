<?php

use damijanc\SimpleXml\XmlDOM;
use PHPUnit\Framework\TestCase;
use Tests\fixtures\Pimcore\DatasheetAT;
use Tests\fixtures\Pimcore\DatasheetDE;
use Tests\fixtures\Pimcore\PimcoreAsset;
use Tests\fixtures\Pimcore\PimcoreImages;
use Tests\fixtures\Pimcore\PimcoreProduct;

final class XmlDOMPimcoreTest extends TestCase
{
    private function preparePimcoreProduct(): PimcoreProduct
    {
        $pimcoreProduct = new PimcoreProduct();

        $pimcoreProduct->pimcoreId = '2345988';
        $pimcoreProduct->sapId = '788922';

        $pimcoreProduct->images = $this->prepareImages();
        $pimcoreProduct->nameDE = 'PLURAZID PURE, 1L Fl ';
        $pimcoreProduct->nameAT = 'PLURAZID PURE, 1L Fl ';

        $pimcoreProduct->manufacturer = '131697';
        $pimcoreProduct->published = 1;

        $pimcoreProduct->datasheetDE = $this->prepareDatasheetDE();
        $pimcoreProduct->datasheetAT = $this->prepareDatasheetAT();

        return $pimcoreProduct;

    }

    private function prepareDatasheetDE(): DatasheetDE
    {
        $pimcoreAsset = new PimcoreAsset();
        $pimcoreAsset->title = 'TitleDE';
        $pimcoreAsset->path = '/somepath/datasheet.pdf';
        $pimcoreAsset->md5 = '7daf75de6faef4e1c6aa2257300a84fe';

        $datasheetDE = new DatasheetDE();
        $datasheetDE->datasheetsDE[] = $pimcoreAsset;

        return $datasheetDE;
    }

    private function prepareDatasheetAT(): DatasheetAT
    {
        $pimcoreAsset = new PimcoreAsset();
        $pimcoreAsset->title = 'TitleAT';
        $pimcoreAsset->path = '/somepath/datasheet.pdf';
        $pimcoreAsset->md5 = '7daf75de6faef4e1c6aa2257300a84fe';

        $datasheetAT = new DatasheetAT();
        $datasheetAT->datasheetsAT[] = $pimcoreAsset;

        return $datasheetAT;

    }

    private function prepareImages(): PimcoreImages
    {
        $pimcoreAsset = new PimcoreAsset();
        $pimcoreAsset->path = '/somepath/picture.jpeg';
        $pimcoreAsset->md5 = 'e8c0086987f3444f5f44263a937ec028';

        $pimcoreImages = new PimcoreImages();
        $pimcoreImages->images = [$pimcoreAsset];
        return $pimcoreImages;
    }

    public function testDatasheetDE()
    {
        $dom = new XmlDOM('1.0', 'utf-8');
        //$dom->formatOutput = true;
        $dom->buildDOM($this->prepareDatasheetDE());

        $xml = $dom->saveXML();
        $this->assertXmlStringEqualsXmlString(file_get_contents(__DIR__.'/fixtures/Pimcore/Datasheet_DE.xml'), $xml);
    }


    public function testPimcoreProduct()
    {
        $dom = new XmlDOM('1.0', 'utf-8');
        //$dom->formatOutput = true;
        $dom->buildDOM($this->preparePimcoreProduct());

        $xml = $dom->saveXML();
        $this->assertXmlStringEqualsXmlString(file_get_contents(__DIR__.'/fixtures/Pimcore/PimcoreProduct.xml'), $xml);
    }
}



