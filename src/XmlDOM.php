<?php
declare(strict_types=1);

namespace damijanc\SimpleXml;

use damijanc\SimpleXml\Attribute\Comment;
use damijanc\SimpleXml\Attribute\Node;
use damijanc\SimpleXml\Attribute\Property;
use DOMDocument;
use DOMElement;
use Exception;
use ReflectionClass;
use ReflectionProperty;

/**
 * Simple XML document generator
 */
class XmlDOM extends DOMDocument
{

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
        $parentElement = $domElement; //class node is always a parent

        $propertyAttributes = $reflectionClass->getAttributes(Property::class);
        $this->handleClassAttributes($propertyAttributes, $parentElement);

        //get all class properties
        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $reflectionProperty) {
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

                    $this->buildDOM($value, $parentElement); //parent is a node we want to attach to
                    $parentElement = $domElement; //setting the parent back
                }
            }

            $propertyAttributes = $reflectionProperty->getAttributes();

            if (count($propertyAttributes) === 0) {
                continue;
            }

            $propertyNodeAttributes = $this->getPropertyNodeAttributes($reflectionProperty, $parentElement, $domElement); //create a node if property is a node

            if ($propertyNodeAttributes) {
                $domElement = null; //we have a parent, we need to create a child
                $this->handleNodeAttributes($propertyNodeAttributes, $parentElement, $domElement); //create a node
            }

            //if node has a property create a property, we can't have a property without a node
            $propertyPropertyAttributes = $reflectionProperty->getAttributes(Property::class);
            $this->handlePropertyAttributes($propertyPropertyAttributes, $domElement, $propertyValue); //create a property

            $propertyCommentAttributes = $reflectionProperty->getAttributes(Comment::class);
            $this->handleCommentAttributes($propertyCommentAttributes, $domElement); //create a comment

            //if we have a node that does not have properties just put out the value
            if ($propertyNodeAttributes) {
                $this->appendText((string)$reflectionProperty->getValue($mixed), $domElement);
            }

        }
    }

    private function getPropertyNodeAttributes(ReflectionProperty $reflectionProperty,?DOMElement &$parentElement, DOMElement &$domElement = null): ?array
    {
        //if property is a node create a node
        $propertyNodeAttributes = $reflectionProperty->getAttributes(Node::class);

        if (count($propertyNodeAttributes) === 0) { //if there is no node there is nothing to do
            return null;
        }

        return $propertyNodeAttributes;

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
    private function handleClassAttributes(array $propertyAttributes, ?DOMElement $parentElement): void
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
    private function handlePropertyAttributes(array $propertyAttributes, ?DOMElement $parentElement, $value): void
    {
        if (count($propertyAttributes) === 0) {
            return;
        }

        foreach ($propertyAttributes as $propertyAttribute) {
            $instance = $propertyAttribute->newInstance();
            if ($instance->value === null) {
                $this->appendAttribute([$instance->key => $value], $parentElement);
                continue;
            }
            $this->appendAttribute([$instance->key => $instance->value], $parentElement);

        }
    }

    /**
     * @param array $commentAttributes
     * @param DOMElement|null $parentElement
     * @return void
     */
    private function handleCommentAttributes(array $commentAttributes, ?DOMElement $parentElement): void
    {
        if (count($commentAttributes) === 0) {
            return;
        }

        $commentAttribute = $commentAttributes[0]->newInstance();
        $this->appendComment($commentAttribute->comment, $parentElement);

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
        $this->makeElement($nodeAttribute->name, $parentNode, $currentNode);
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

    private function appendText(string $text, DOMElement &$domElement): void
    {
        $domElement->appendChild($this->createTextNode($text));
    }

    private function appendCData(string $text, DOMElement &$domElement): void
    {
        $domElement->appendChild($this->createCDATASection($text));
    }

    private function appendComment(string $text, DOMElement &$domElement): void
    {
        $domElement->appendChild($this->createComment($text));
    }
}
