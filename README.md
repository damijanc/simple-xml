simple-xml
==========

Simple wrapper for PHP (http://php.net/manual/en/book.ftp.php) DOMDocument

Motivation for this class was to simplify creation of XML files 
so instead of doing:

Usage
-----

```php
require_once 'XmlDOM.class.php';

$arr = array(
                  '@attributes' => array(
                  'type' => 'fiction'
                  ),
                  'book' => array(
                      array(
                        '@attributes' => array(
                              'author' => 'George Orwell'
                         ),
                         'title' => '1984'
                      ),
                       array(
                        '@attributes' => array(
                              'author' => 'Lojze Slak'
                         ),
                         'title' => 'S harmoniko po svetu'
                      ),
                      array(
                        '@attributes' => array(
                              'author' => 'Isaac Asimov'
                        ),
                         'title' => array('@cdata' => 'Foundation'),
                        'price' => '$15.61'
                      ),
                      array(
                        '@attributes' => array(
                              'author' => 'Robert A Heinlein'
                        ),
                        'title' => array('@cdata' => 'Stranger in a Strange Land'),
                        'special' => array(
                                    array(
                                      '@attributes' => array(
                                          'discount' => '10%',
                                    ),
                                     '@value'=> '10$',
                                  ),
                        ),
                        'price' => '$43.29'
                      )
                  )//end of book
           ); //end of books

$dom = new XmlDOM('1.0', 'utf-8');
$dom->formatOutput = true;
$dom->BuildDOM($arr,'books');

$xml = $dom->saveXML();

echo $xml;
```
Outputs:
```xml
<?xml version="1.0" encoding="utf-8"?>
<books type="fiction">
  <book author="George Orwell">
    <title>1984</title>
  </book>
  <book author="Lojze Slak">
    <title>S harmoniko po svetu</title>
  </book>
  <book author="Isaac Asimov">
    <title><![CDATA[Foundation]]></title>
    <price>$15.61</price>
  </book>
  <book author="Robert A Heinlein">
    <title><![CDATA[Stranger in a Strange Land]]></title>
    <special discount="10%">10$</special>
    <price>$43.29</price>
  </book>
</books>
```


TODO:
----

- improve error handling
- remove non printable chars
- ...


Check CHANGELOG for updates.
