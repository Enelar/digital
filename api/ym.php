<?php

class ym extends api
{
  protected function Reserve()
  {
  }
  
  protected function ModelSpec( $id )
  {
    $id = (int)$id;
    $res = system("phantomjs parse.js 2 $id");
    return json_decode($res, true);
  }
}