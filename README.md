simple-xml
==========

Simple wrapper for PHP (http://www.php.net/manual/en/class.domdocument.php) DOMDocument

After roughly 11 years this lib is finally getting and update. It uses a nice PHP feature - properties :D

# Something about XML itself
Valid XML needs to start with xml version and encoding declaration like ```<?xml version="1.0" encoding="utf-8"?>```. XML without it is invalid.

Library by default uses UTF-8 encoding. So please make sure that all your strings are properly encoded.

XML supports multiple type of text: text, CDATA and an element. 

Everything in [CDATA](https://en.wikipedia.org/wiki/CDATA) section will be interpreted as a literal string. For example ```<![CDATA[<sender>John Smith</sender>]]>``` 
will be interpreted as ```&lt;sender&gt;John Smith&lt;/sender&gt;```, as literal string. To be compatible with [XML Specs](https://www.w3.org/TR/xml/#syntax) you can use CDATA if your sting contains chars like <, >, & etc.

Text attribute will make sure that same characters will be escaped. ```&``` will be ```&amp;```, ```>``` will be ```&gt;``` and ```<``` will become ```&lt;```. Up to you what you want to use.

If nothing is selected value will be used as is. Maybe I will add some formatters later like date and currency as they are supported in PHP.

# Installation

```bash
composer require damijanc/simple-xml
```

# Usage

We need to define a class with attributes to tell the library what kind of XML we want to have. At the moment setters and getters are no supported, but I might add that later. If property is an array we expect to have array of classes that are similarly decorated with attributes

In the example we have a collection of books. For that we need to define a collection class books and a books class, as in the example below.

```php
use damijanc\SimpleXml\Attribute\Node;
use damijanc\SimpleXml\Attribute\Property;

#[Node('books')]
#[Property('type', 'fiction')]
class Books
{
    public array $books;
}
```


```php
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
```


Outputs:
```xml
<?xml version="1.0" encoding="utf-8"?>
<books type="fiction">
    <book>
        <author author="George Orwell">George Orwell</author>
        <title>1984</title>
    </book>
    <book>
        <author author="Lojze Slak">Lojze Slak</author>
        <title>S harmoniko po svetu</title>
    </book>
    <book>
        <author author="Isaac Asimov">Isaac Asimov</author>
        <title>Foundation</title>
        <price>$15.61</price>
    </book>
    <book>
        <author author="Robert A Heinlein">Robert A Heinlein</author>
        <title>Stranger in a Strange Land</title>
        <price>$43.29</price>
    </book>
</books>
```

Note on comments:
As comments can appear anywhere in the XML it is hard to predict the user's intention. I decided to add comment support on the node attribute level

for example:
```php

    #[Node('title')]
    #[Comment('This is a comment')]
    public string $title;
```

will result in:

```xml
<title><!--This is a comment-->1984</title>
```

I might change this implementation later. For now, I do not have any real use case.

# Supported attributes

```php
#[Node('book')]
class Book
{
    #[Property('name', null)]
    #[Property('data-link', 'some-dummy-link')]
    public string $author;
} 

```
Propery attribute will add a propery to a node. It accepts two values name and value. If value is null it will be read from class property.
We also have a support for multiple properties attributes that can added to mutiple class properties. Example above will attach properties 
to parent node as Node attribute is not defined. In the case in the example we would get
```xml
<book name="George Orwell" data-link="some-dummy-link"/>
```

Another example with Node attribute
```php
#[Node('book')]
class Book
{
    #[Node('author')]
    #[Property('data-link', 'some-dummy-link')]
    public string $author;
} 

```
will result in:
```xml
<book data-link="some-dummy-link">
    <author>George Orwell</author>
</book>
```



## Property


# A word about performance

I did manage to generate XML from 50k classes in 300ms, therefor I believe it is fast enough. Would be nice if someone can confirm it.

