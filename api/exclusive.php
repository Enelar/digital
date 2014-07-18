<?php

class exclusive extends api
{
  protected function Reserve()
  {
    return
    [
      "design" => "pages/exclusive",
      "result" => "content",
    ];
  }
}