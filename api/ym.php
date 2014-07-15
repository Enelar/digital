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
    var_dump($parsed);
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

  protected function Model( $id )
  {
    $id = (int)$id;
    $res = exec("phantomjs --debug=no parse.js 0 $id");
    $parsed = json_decode($res, true);    
    var_dump($parsed);
    return ['data' => $parsed];
  }


  protected function UpdatePrices( $offset = 0 )
  {
    $offset = (int)$offset;
    $res = db::Query("SELECT * FROM phones.model_params WHERE param=3 LIMIT 1 OFFSET $offset");
    foreach ($res as $row)
    {
      var_dump($row);
      $res = exec("phantomjs --debug=no parse.js 0 {$row['value']}");
      var_dump($res);
      $parsed = json_decode($res, true);   
      var_dump($parsed);
      $price = $parsed['prices'][3];
      $price += 30;
      db::Query("UPDATE phones.models SET price=$2 WHERE id=$1", [$row['model'], $price]);
      var_dump([$row['model'], $price]);
    }
    return $res;
  }
}