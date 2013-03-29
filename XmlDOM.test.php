<?php

require_once 'XmlDOM.class.php';

//complex example
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
$dom->generateXML($arr,'books');

$xml = $dom->saveXML();

echo $xml;

//and more simple example
$arr = array(     'publishTime' => '18.01.2013 11:09:39',
                      'date' => '18.01.2013',
                      'title' => '18.01.2013',
                      'publish' => 'False',
                      'description' => 'delo',
      );

$dom = new XmlDOM('1.0', 'utf-8');
$dom->formatOutput = true;
$dom->generateXML($arr, 'issue');

$xml = $dom->saveXML();

echo $xml;

?>
