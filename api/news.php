<?php

class news extends api
{
  protected function Reserve()
  {
    return array
    (
      "design" => "pages/news",
      "result" => "content"
    );
  }
}
