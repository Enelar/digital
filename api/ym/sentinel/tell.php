<?php

class tell extends api
{
  protected function LastPrice( $ymid )
  {
    $res = db::Query("SELECT * FROM market.slices WHERE ymid=$1 AND array_length(price, 1) > 0 ORDER BY snap DESC LIMIT 1", [$ymid], true);
    return ["data" => $res];
  }
}