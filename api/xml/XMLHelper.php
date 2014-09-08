<?php

class XMLHelper
{
  private $creater;
  private $root;
  private $childs = [];
  public function __construct($a = null, $b = null)
  {
    if (!is_null($a))
      $this->creater = $this->root = new DOMDocument($a, $b);
  } 
    
  public function createElement( $name )
  {
    $el = new XMLHelper();
    $el->creater = $this->creater;
    $el->root = $this->creater->createElement($name);
    array_push($this->childs, $el);
    return $el;
  }
  
  public function __toString()
  {
    return $this->GenerateXML()->saveXML();
  }
  
  private function GenerateXML()
  {
    foreach ($this->childs as $child)
      $this->root->appendChild($child->GenerateXML());
    return $this->root;
  }
  
  public function __call($name, $args)
  {
    call_user_func_array([$this->root, $name], $args);
    return $this; 
  }
  
  public function createTextNode($text)
  {
    $el = new XMLHelper();
    $el->creater = $this->creater;
    $el->root = $this->creater->createTextNode($text);
    array_push($this->childs, $el);
    return $el;
  }
}
