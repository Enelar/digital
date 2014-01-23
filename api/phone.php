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
  
  private function GetValueByName( $phone, $field )
  { // deprecated
    $res = db::Query("
    WITH param_id AS
    (
      SELECT id FROM phones.params WHERE name=$1
    ), all_model_values AS
    (
      SELECT value as id FROM phones.model_values WHERE model=$2
    ), all_that_type_values AS
    (
      SELECT param_values.* FROM phones.param_values, param_id WHERE param_values.param=param_id.id
    )
    SELECT value FROM all_that_type_values, all_model_values WHERE all_that_type_values.id=all_model_values.id
    ", array($field, $phone), true);
    if (!count($res))
      return null;
    return $res['value'];
  }
  
  protected function GetMinimalInfo( $id )
  {
    return array(
      "data" => array(
        "picture" => $this->GetValueByName($id, "picture"),
        "name" => $this->GetValueByName($id, "name"),
        "colour" => $this->GetValueByName($id, "colour"),
      )
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