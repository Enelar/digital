<?php

class contacts extends api
{
  protected function Reserve()
  {
    return array
    (
      "design" => "pages/contacts",
      "result" => "content"
    );
  }
  
  protected function Juridical()
  {
    return array
    (
      "design" => "pages/juridical",
      "result" => "content",
      "data" => ["super" => true],      
    );
  }

  protected function Juridic()
  {
    return array
    (
      "design" => "pages/juridical",
      "result" => "content",
      "data" => ["super" => true],
    );
  }

}
