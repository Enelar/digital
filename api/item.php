<?php

class item extends api
{
  protected function Reserve()
  {
    return array
    (
      "design" => "catalog/item",
      "result" => "content",
      "data" => array
      (
        "description" => "Blah blah blah",
        "title" => "Blah i3780"
      )
    );
  }
  protected function Test($id)
  {
    //$model = db::Query("SELECT * FROM phones.models WHERE id=$1", array($id), true);
    //phoxy_protected_assert(count($model), array("error" => "Телефон не найден"));
    //$vendor = db::Query("SELECT * FROM phones.vendor WHERE id=$1", array($model['vendor']), true);
    //$params = db::Query("
      
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