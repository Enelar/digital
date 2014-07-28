<?php

class pricelist extends api
{
  protected function Reserve()
  {
    $res = db::Query("SELECT * FROM phones.models WHERE vendor != 580 ORDER BY quantity DESC, view_weight DESC");
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
    return db::Query("SELECT * FROM phones.models WHERE id=$1", [$id], true);
    return ["cache" => ["no" => "global"]];
  }

  protected function import( )
  {
    global $_POST;
    global $_FILES;    

    if (!count($_FILES))
    {
      return
      [
        "design" => "admin/pricelist/import",
        "result" => "content",
      ];
    }

    var_dump($_FILES);
  }
}
