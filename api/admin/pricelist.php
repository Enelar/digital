<?php

class pricelist extends api
{
  protected function Reserve()
  {
    $res = db::Query("SELECT * FROM phones.models WHERE vendor != 580 ORDER BY quantity DESC, id");
    return ["design" => "admin/pricelist/body", "result" => "content", "cache" => ["no" => "global"], "data" => ["pricelist" => $res]];
  }
  
  protected function UpdatePrice( $id, $price )
  {
    db::Query("UPDATE phones.models SET price=$2 WHERE id=$1", [$id, $price]);
    return ["cache" => ["no" => "global"]];
  }
  
  protected function UpdateQuantity( $id, $amount )
  {
    db::Query("UPDATE phones.models SET quantity=$2 WHERE id=$1", [$id, $amount]);
    return ["cache" => ["no" => "global"]];
  }
}