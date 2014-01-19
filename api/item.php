<?php

class item extends api
{
  protected function Reserve()
  {
    return array
    (
      "design" => "catalog/item",
      "result" => "content",
      "data" => array
      (
        "description" => "Blah blah blah",
        "title" => "Blah i3780"
      )
    );
  }
}