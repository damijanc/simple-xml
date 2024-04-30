<?php

namespace Tests;

use damijanc\SimpleXml\XmlDOM;
use Tests\fixtures\Book;
use Tests\fixtures\Books;
use PHPUnit\Framework\TestCase;

final class XmlDOMTest extends TestCase
{
    private function prepareBooks(): Books
    {
        $issue = new Books();
        $issue->books = [];

        $book = new Book();
        $book->author = 'George Orwell';
        $book->title = '1984';
        $issue->books[] = $book;

        $book = new Book();
        $book->author = 'Lojze Slak';
        $book->title = 'S harmoniko po svetu';
        $issue->books[] = $book;

        $book = new Book();
        $book->author = 'Isaac Asimov';
        $book->title = 'Foundation';
        $book->price = '$15.61';
        $issue->books[] = $book;

        $book = new Book();
        $book->author = 'Robert A Heinlein';
        $book->title = 'Stranger in a Strange Land';
        $book->price = '$43.29';
        $issue->books[] = $book;


        $book = new Book();
        $book->author = 'Damijanc';
        $book->title = '&><';
        $book->price = '$43.29';
        $issue->books[] = $book;

        return $issue;
    }


    public function testBooks()
    {
        $dom = new XmlDOM('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->buildDOM($this->prepareBooks());

        $xml = $dom->saveXML();

        $this->assertXmlStringEqualsXmlString(file_get_contents(__DIR__.'/fixtures/books.xml'), $xml);
    }

    public function testBook()
    {
        $book = new Book();
        $book->author = 'George Orwell';
        $book->title = '1984';
        $book->price = '$9.99';
        $book->genre = 'sci-fi';

        $dom = new XmlDOM('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->buildDOM($book);

        $xml = $dom->saveXML();
        $this->assertXmlStringEqualsXmlString(file_get_contents(__DIR__.'/fixtures/book.xml'), $xml);

    }
}



