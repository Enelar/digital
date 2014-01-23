<?php

class import extends api
{
  protected function Reserve()
  {
    $content = file_get_contents("http://www.digital812.ru/xml.php");
    $root = new SimpleXMLElement($content);
    
    $ret = array();
    $shop = $root->shop;
    foreach ($shop->offers[0] as $offer)
    {
      $model = array();
      $model['id'] = (int)$offer['id'];
      $model['price'] = (int)$offer->price;
      $model['vendor'] = (string)$offer->vendor;
      
      $params = array();
      array_push($params, array('name' => 'picture', 'value' => (string)$offer->picture));      
      
      array_push(
        $params, 
        array(
          'name' => 'name',
          'value' => 
            (string)$offer->name == '' ?
            (string)$offer->model :
            (string)$offer->name
        ));
      
      foreach ($offer->param as $param)
        array_push($params, array('name' => (string)$param['name'], 'value' => (string)$param));
      
      $model['params'] = $params;
      array_push($ret, $model);
    }
    
    foreach ($ret as $model)
    {
      $vendor_id = $this->GetVendorId($model['vendor']);
      db::Query("INSERT INTO phones.models(id, vendor, price) VALUES ($1, $2, $3)",
        array($model['id'], $vendor_id, $model['price']));
      foreach ($model['params'] as $param)
      {
        $id = $this->GetParamValueId($param['name'], $param['value']);
        db::Query("INSERT INTO phones.model_values(model, value) VALUES ($1, $2)",
          array($model['id'], $id));
      }
    }
  }
  
  protected function GetParamId( $name )
  {
    $ret = db::Query("SELECT id FROM phones.params WHERE name=$1::varchar", array($name), true);
    if (isset($ret['id']))
      return (int)$ret['id'];
    $ret = db::Query("INSERT INTO phones.params(name, type) VALUES ($1, 'varchar') RETURNING id",
      array($name), true);
    return (int)$ret['id'];
  }
  
  protected function GetParamValueId( $name, $value )
  {
    $id = $this->GetParamId($name);
    $ret = db::Query("SELECT id FROM phones.param_values WHERE param=$1 AND value=$2", array($id, $value), true);
    if (isset($ret['id']))
      return (int)$ret['id'];
    $ret = db::Query("INSERT INTO phones.param_values(param, value) VALUES ($1, $2) RETURNING id",
      array($id, $value), true);
    return (int)$ret['id'];
  }
  
  protected function GetVendorId( $name )
  {
    $ret = db::Query("SELECT id FROM phones.vendor WHERE name=$1::varchar", array($name), true);
    if (isset($ret['id']))
      return (int)$ret['id'];
    $ret = db::Query("INSERT INTO phones.vendor(id, name) VALUES ((random() * 1000)::int2, $1) RETURNING id",
      array($name), true);
    return (int)$ret['id'];  
  }
}