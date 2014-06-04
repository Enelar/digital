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
      "result" => "content"
    );
  }

}
