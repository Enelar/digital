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
  
  protected function Root()
  {
    $res = db::Query("SELECT * FROM phones.vendor");
    return array(
      "design" => "catalog/vendor_select",
      "result" => "content",
      "data" => array("vendors" => $res)
    );
  }
  
  protected function Vendor( $name )
  {
    $res = db::Query(
      "SELECT id, price, (quantity > 0) as available FROM phones.models WHERE vendor=(SELECT id FROM phones.vendor WHERE name=$1)",
      array($name));
    $phone = LoadModule('api', 'phone');
    foreach ($res as &$r)
    {
      $i = $phone->GetMinimalInfo($r['id']);
      $r = array_merge($r, $i); 
    }
    return array(
      "design" => "catalog/vendor",
      "result" => "content",
      "data" => array("vendor" => $res, "catalog" => (count($res) ? $name : ''))
    );
  }
}
