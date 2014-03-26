<?php

class _phone extends api
{
  protected function Reserve()
  {
    return array(
      "design" => "ondev",
      "result" => "content"
    );
  }
  
  protected function Vendor($name)
  {
    $res = db::Query(
      "SELECT id, price, (quantity > 0) as available, view_weight
       FROM phones.models WHERE vendor=(SELECT id FROM phones.vendor WHERE name=$1)
       ORDER BY view_weight ASC",
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
  
  protected function Item( $id )
  {
    $t = array
    (
      "design" => "catalog/item",
      "result" => "content",
    );
    
    $m = LoadModule('api', 'phone', true);
    $ret = array_merge($t, $m->Reserve($id));
    
    $ret['data']['id'] = (int)$id;
    
    return $ret;  
  }
}
