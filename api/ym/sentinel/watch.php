<?php

class watch extends api
{
  protected function Reserve()
  {
    $id = 10495457;
    $obj = $this->Grab($id);
    if (!$obj['success'])
      return false;
    $res = db::Query("INSERT INTO market.slices(ymid, price, shop) VALUES ($1, $2::character varying[]::integer[], $3) RETURNING snap",
      [$id, $obj['prices'], $obj['shops']], true);
    return $true;
  }

  private function Grab( $id )
  {
    $g = LoadModule('api/grab', 'grabber');
    $obj = json_decode($g->Grab($id), true);
    if ($obj['success'])
      $this->PageCache($obj['url'], $obj['body'], $obj['shot']);
    return $obj;
  }

  private function PageCache( $url, $body, $img )
  {
    $res = db::Query("INSERT INTO market.pagecache(url, body, img) VALUES ($1, $2, decode($3, 'base64')) RETURNING snap",
      [$url, $body, $img], true);
    var_dump($res);
  }
}