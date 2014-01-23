<?php

class phone extends api
{
  protected function Reserve( $phone )
  {
    return array(
      "script" => array("js/main/phone.js"),
      "before" => "BindPhoneInfo",
      "data" => $this->GetParams($phone)['data']
    );
  }

  protected function GetParams( $id )
  {
    $res = db::Query("SELECT * FROM phones.model_values WHERE model=$1", array($id));
    $ret = array();
    foreach ($res as $t)
      array_push($ret, $t['value']);
    return array("data" => array("params" => $ret));
  }
  
  protected function ExplainValues( $ids )
  {
    if (!is_array($ids))
      $ids = array($ids);
      
    $ret = array();
    foreach ($ids as $id)
    {
      $res = db::Query("SELECT param as k, value as v FROM phones.param_values WHERE id=$1::int4", array($id), true);
      if (!count($res))
        continue;
      $ret[(int)$id] = $res;
    }
    return array("data" => array("values" => $ret));
  }
  
  protected function ExplainTypes( $params )
  {
    if (!is_array($params))
      $params = array($params);
      
    $ret = array();
    foreach ($params as $param)
    {
      $res = db::Query('SELECT name, hide, "group" FROM phones.params WHERE id=$1', array($param), true);
      $res['hide'] = ($res['hide'] == 't');
      $ret[(int)$param] = $res;
    }
    return array("data" => array("types" => $ret));
  }
  
  protected function GetGroups( )
  {
    $res = db::Query("SELECT * FROM phones.param_groups");
    $ret = array();
    
    foreach ($res as $v)
    {
      $k = $v['id'];
      unset($v['id']);
      $ret[$k] = $v;
    }
    return array("data" => array("groups" => $ret));
  }
}