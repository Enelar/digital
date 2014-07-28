<?php

class bot extends api
{
  protected function Reserve()
  {
    $res = db::Query('SELECT models.id, value FROM "phones"."models", phones.model_params WHERE models.id=model_params.model AND param=171 ORDER BY value DESC');
    $i = 1;
    foreach ($res as $row)
      db::Query("UPDATE phones.models SET view_weight=$2 WHERE id=$1", array($row['id'], $i++));
  }
}
