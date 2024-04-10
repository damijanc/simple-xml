<?php
declare(strict_types=1);

namespace damijanc\SimpleXml;

use damijanc\SimpleXml\Attribute\Node;
use damijanc\SimpleXml\Attribute\Property;
use damijanc\SimpleXml\Attribute\RootNode;
use DOMDocument;
use DOMElement;
use Exception;
use ReflectionClass;

/**
 * Simple XML document generator
 */
class XmlDOM extends DOMDocument
{

    /**
     * Removes non-printable chars from string
     * @param string $in_str input string
     * @param string $charset charset, defaults to UTF-8
     * @return string string without non-printable chars
     */
    public function removeNonPrintable($in_str, $charset = 'UTF-8')
    {
        #remove all non utf8 characters
        $in_str = mb_convert_encoding($in_str, $charset, $charset);

        #Remove non printable character (i.e. below ascii code 32).
        $in_str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $in_str);

        return $in_str;
    }

    private function isValidClass(mixed $x): bool
    {
        // not an object, not an instance
        if (!is_object($x)) {
            return false;
        }

        return ($x = get_class($x)) && $x !== 'stdClass';
    }

    /**
     * @param array $propertyAttributes
     * @param DOMElement|null $parentElement
     * @return void
     */
    public function handleClassAttributes(array $propertyAttributes, ?DOMElement $parentElement): void
    {
        if (count($propertyAttributes) === 0) {
            return;
        }

        $this->handlePropertyAttribute($propertyAttributes[0]->newInstance(), $parentElement);
    }

    /**
     * @param array $propertyAttributes
     * @param DOMElement|null $parentElement
     * @return void
     */
    public function handlePropertyAttributes(array $propertyAttributes, ?DOMElement $parentElement,  $value): void
    {
        if (count($propertyAttributes) === 0) {
            return;
        }

        $propertyAttribute = $propertyAttributes[0]->newInstance();
        $this->appendAttribute([$propertyAttribute->key => $value], $parentElement);

    }

    private function handleNodeAttributes(array $nodeAttributes, ?DOMElement &$parentElement, DOMElement &$domElement = null): void
    {
        if (count($nodeAttributes) === 0) {
            return;
        }

        $this->handleNodeAttribute($nodeAttributes[0]->newInstance(), $parentElement, $domElement);
    }

    private function handlePropertyAttribute(Property $propertyAttribute, ?DOMElement $parentElement): void
    {
        $this->appendAttribute([$propertyAttribute->key => $propertyAttribute->value], $parentElement);
    }

    private function handleNodeAttribute(Node $nodeAttribute, ?DOMElement &$parentNode, DOMElement &$currentNode = null): void
    {
        $this->makeElement($nodeAttribute->name, $parentNode,  $currentNode);
    }

    public function buildDOM(object $mixed, DOMElement &$parentElement = null): void
    {
        if ($this->isValidClass($mixed) === false) {
            throw new Exception('You must pass a class as a parameter');
        }

        //reflect class
        $reflectionClass = new ReflectionClass($mixed);

        //class can only have node and property attributes,
        // we need to create node before we can set attribute to it

        $nodeAttributes = $reflectionClass->getAttributes(Node::class);
        $this->handleNodeAttributes($nodeAttributes, $parentElement, $domElement);

        $propertyAttributes = $reflectionClass->getAttributes(Property::class);
        $this->handleClassAttributes($propertyAttributes, $parentElement);

        //get all properties
        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $reflectionProperty)
        {
            if ($reflectionProperty->isInitialized($mixed) === false) {
                continue;
            }

            $reflectionProperty->setAccessible(true); //if property is private/protected we need to make it accessible
            $propertyValue = $reflectionProperty->getValue($mixed); //you need to have an instance of a class to be able to get a value


            if ($reflectionProperty->getType()->getName() === 'array') {
                foreach ($propertyValue as $value) {
                    if (!$this->isValidClass($value)) {
                        continue;
                    }

                    $this->buildDOM($value,$parentElement);
                }
            }

            $propertyAttributes = $reflectionProperty->getAttributes();

            if (count($propertyAttributes)  === 0) {
                continue;
            }


            //if property is a node create a node
            $domElement = null; //we have a parent, we need to create a child
            $propertyNodeAttributes = $reflectionProperty->getAttributes(Node::class);
            $this->handleNodeAttributes($propertyNodeAttributes, $parentElement, $domElement);

            //if mode has a property create a property
            $propertyPropertyAttributes = $reflectionProperty->getAttributes(Property::class);
            $this->handlePropertyAttributes($propertyPropertyAttributes, $domElement, $propertyValue);

        }

    }

    private function makeElement(string $nodeName, &$parentNode, &$currentNode)
    {
        if (is_null($currentNode)) { //if we have a node we do nothing
            $currentNode = $this->createElement($nodeName);
            if (is_null($parentNode)) {
                $parentNode = $currentNode;
                //if we have no parent append it to the root
                $this->appendChild($currentNode);
            } else {
                $parentNode->appendChild($currentNode);
            }
        }
    }

    private function appendAttribute(array $arr, DOMElement &$domElement)
    {
        if (is_array($arr)) {
            //attributes must be key/value pairs and can't have children
            foreach ($arr as $key => $value) {
                $domAttribute = $this->createAttribute($key);
                $domAttribute->value = $value;
                $domElement->appendChild($domAttribute);
            }
        }
    }

    private function appendCData(array $arr, DOMElement &$domElement)
    {
        if (is_array($arr)) {
            //attributes must be key/value pairs and can't have childs
            foreach ($arr as $value) {
                $domElement->appendChild($this->createCDATASection($value));
            }
        }
    }
}
