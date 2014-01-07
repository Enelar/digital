<?php

class catalog extends api
{
  protected function Reserve()
  {
    $test_mobile_catalog = array(
      100 =>
      array(
        "url" => "testUrl1",
        "name" => "testName1",
        "price" => 29300,
        ),
      200 =>
        array(
        "url" => "/testUrl2",
        "name" => "testName2",
        "price" => 30000,
        ),
    );
    return array
    (
      "design" => "pages/catalog",
      "result" => "content",
      "data" => array("catalog" => $test_mobile_catalog),
    );
  }
}
