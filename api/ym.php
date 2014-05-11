<?php

class ym extends api
{
  protected function Reserve()
  {
  }
  
  protected function ModelSpec( $id )
  {
    $id = (int)$id;
    $res = exec("phantomjs --debug=no parse.js 2 $id");
    $parsed = json_decode($res, true);    
    return ['data' => $parsed];
  }
  
  protected function Mapping()
  {
    $res = db::Query("SELECT * FROM phones.ym_mapping");
    $ret = [];
    foreach ($res as $row)
      $ret[$row['ym_name']] = $row['param'];
    return $ret;
  }
}