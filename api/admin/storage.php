<?php

class storage extends api
{
  protected function Reserve()
  {
    return array(
      "design" => "admin/storage",
      "result" => "content"
    );
  }
  
  protected function SearchByIMEI($q)
  {
    $ret = array(
      'known' => false
    );
    return array(
      "script" => "admin/storage",
      "routeline" => "SearchByIMEI",
      "data" => $ret
    );
  }
  
  protected function SearchByModel($q)
  {
    $ret = array(
      'known' => false
    );
    return array(
      "script" => "admin/storage",
      "routeline" => "SearchByModel",
      "data" => $ret
    );
  }
}
