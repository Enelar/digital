<?php

class cp extends api
{
  protected function Reserve()
  {
    return
    [
      "design" => "ym/prices/entry"
    ];
  }

  protected function GetList()
  {
    $res = db::Query("SELECT *, extract(epoch from now() - snap)::int / 60 as sync FROM market.cards ORDER BY name NULLS FIRST");
    return
    [
      "design" => "ym/prices/table",
      "data" => ["list" => $res],
    ];
  }

  protected function AddToWatch($str)
  {
    list($a, $b) = explode("modelid=", $str, 2);
    list($id, $garb) = explode("&", $b, 2);
    $watch = LoadModule('api/ym/sentinel', 'watch');
    $watch->Add($id);
  }

  protected function Spectate()
  {
    $res = db::Query("
      WITH last_slices AS
      (
        SELECT slices.ymid as id, max(slices.snap), name
          FROM market.slices, market.cards
          WHERE slices.ymid=cards.ymid
            AND array_length(price, 1) > 0
          GROUP BY slices.ymid, name
      )
      SELECT slices.*, name, extract(epoch from now() - snap)::int / 60 as sync
        FROM market.slices, last_slices
        WHERE id=ymid AND max=snap
        ORDER BY name ASC
    ");

    return
    [
      "design" => "ym/prices/spectate",
      "data" => ["snap" => $res],
    ];
  }
}