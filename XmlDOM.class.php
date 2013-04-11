<?php

/**
 * Simple XML document generator
 */
class XmlDOM extends DOMDocument {

  private $json = array();

  /**
   * Removes non printable chars from string
   * @param type $in_str input string
   * @param type $charset charset, defaults to UTF-8
   * @return type string without non printable chars
   */
  public function RemoveNonPrintable ($in_str, $charset = 'UTF-8') {
    #remove all non utf8 characters
    $in_str = mb_convert_encoding($in_str, $charset, $charset);

    #Remove non printable character (i.e. below ascii code 32).
    $in_str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $in_str);

    return $in_str;
  }

  /**
   * Generates XML from array
   * @param array $mixed array to generate xml from
   * @param type $NodeName
   * @param DOMElement $parentElement
   * @throws Exception
   */
  public function BuildDOM (array $mixed, $NodeName, DOMElement &$parentElement = null) {

    if (empty($NodeName))
      throw new Exception('You must pass node name to use this.');

    //create element
    $this->MakeElement($domElement, $parentElement, $NodeName);

    foreach ($mixed as $key => $value) {

      switch ($key) {
        case '@attributes':
          //if we have attribute we must attach it to $domElement
          $this->AppendAttribute($value, $domElement);
          break;

        case '@value':
          $domElement->nodeValue = $value;
          break;

        case '@cdata':
          $domElement->appendChild($this->createCDATASection($value));
          break;

        default:
          //we have multiple values of the same type
          if (is_array($value)) {
            if (key($value) === '@cdata') {
              $childElement = $this->createElement($key);
              $this->AppendCData($value, $childElement);
              $domElement->appendChild($childElement);
            }
            else {
              foreach ($value as $k => $v) {
                $this->BuildDOM($v, $key, $domElement);
              }
            }
          }
          //key/value pair, nothing to loop trough
          else {
            $domElement->appendChild($this->createElement($key, $value));
          }
          break;
      }
    }
  }

  /**
   * Generates XML from CSV file, where first line must be column names
   * @param type $file path to CSV file
   * @param type $rootNodeName root xml element name, defaults to elements
   * @param type $nodeName node name for child element, defaults to element
   * @param type $delimiter delimiter for csv, defaults to comma
   * @param type $locale locate to be used when reading CSV, defaults to en_US
   */
  public function generateFromCSV ($file, $rootNodeName = 'elements', $nodeName = 'element', $delimiter = ',', $locale = 'en_US') {

    $t = $this->LoadCSV($file, $delimiter, $locale);
    $arr[$nodeName] = $t;
    $this->BuildDOM($arr, $rootNodeName);
  }

  /**
   * Function return dom document as JSON encoded string
   * @param boolean $prettyPrint if you want pretty print set it to true
   * @return string json encoded string
   */
  public function saveJSON($prettyPrint = FALSE) {
    $a = $this->toArray();
    if ($prettyPrint) {
      return $this->prettyPrint(json_encode($a));
    }
    return json_encode($a);
  }

  private function toArray ($parentNode = NULL, array &$a = NULL) {

    if (is_null($parentNode)) {
      $parentNode = $this;
    }

    if (!is_array($a)) {
      $a = array();
    }
    //get the attributes
    if ($parentNode instanceof DOMElement) {
      $this->GetAttributesJSON($parentNode, $a);
    }

    if ($parentNode->hasChildNodes()) {
      foreach ($parentNode->childNodes as $childNode) {
        if ($childNode instanceof DOMElement) {
          $b = array();
          array_push($a, $this->toArray($childNode, $b));
          continue;
        }
      }
    }

    if ($childNode instanceof DOMText || $childNode instanceof DOMCdataSection) {
      array_push($a, array($childNode->parentNode->nodeName => $childNode->nodeValue));
    }

    return $a;
  }

  /**
   * Function borrowed from http://stackoverflow.com/questions/6054033/pretty-printing-json-with-php
   * @param type $json
   * @return string
   */
  private function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if( $char === '"' && $prev_char != '\\' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
}

  private function GetAttributesJSON (DOMElement $c, array &$a) {

    foreach ($c->attributes as $attribute_name => $attribute_node) {
      array_push($a, array($attribute_name => $attribute_node->nodeValue));
    }
  }

  private function MakeElement (&$domElement, &$parentElement, $NodeName) {
    if (is_null($domElement)) {
      $domElement = $this->createElement($NodeName);
      if (is_null($parentElement)) {
        $parentElement = $domElement;
        //if we have no parent append it to the root
        $this->appendChild($domElement);
      }
      else {
        $parentElement->appendChild($domElement);
      }
    }
  }

  private function AppendAttribute (array $arr, DOMElement &$domElement) {
    if (is_array($arr)) {
      //attributes must be key/value pairs and can't have childs
      foreach ($arr as $key => $value) {
        $domAttribute = $this->createAttribute($key);
        $domAttribute->value = $value;
        $domElement->appendChild($domAttribute);
      }
    }
  }

  private function AppendCData (array $arr, DOMElement &$domElement) {
    if (is_array($arr)) {
      //attributes must be key/value pairs and can't have childs
      foreach ($arr as $value) {
        $domElement->appendChild($this->createCDATASection($value));
      }
    }
  }

  private function LoadCSV ($file, $delimiter = ',', $locale = 'en_US') {

    if (!is_file($file)) {
      throw new Exception('File ' . $file . ' does not exist.');
      return;
    }

    //output array
    $out = array();

    //we must set locale in order to parse CSV correctly
    //http://static.zend.com/topics/multibyte-fgetcsv.pdf
    setlocale(LC_ALL, $locale);

    //open file for reading
    if (($handle = fopen($file, 'r')) !== FALSE) {

      //we will not limit the line lenght
      $keys = fgetcsv($handle, 0, $delimiter); //get header

      while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
        $t = array();
        if (count($data) == count($keys)) {
          for ($index = 0; $index < count($data); $index++) {
            $t[$keys[$index]] = $data[$index];
          }
          $out[] = $t;
        }
      }
      fclose($handle);

      return $out;
    }
  }

}

?>
