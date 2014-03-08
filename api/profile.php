<?php

class profile extends api
{
  protected function Reserve()
  {
    return array(
      "design" => "profile/main",
      "result" => "content",
    );
  }
}