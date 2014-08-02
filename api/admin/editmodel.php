<?php

class editmodel extends api
{
  protected function Reserve()
  {
    $res = db::Query("SELECT id FROM phones.models WHERE show=true ORDER BY id ASC");
    $ret = [];
    foreach ($res as $row)
      array_push($ret, $row['id']);
    return [
      "design" => "admin/editmodel/list",
      "result" => "content",
      "data" => ["models" => $ret],
      "cache" => ["no" => "global"]
    ];
  }
  
  protected function ModelById( $id )
  {
    $res = db::Query("SELECT value as name FROM phones.model_params WHERE model=$1 AND param=1", [$id], true);
    if (!count($res))
      $res['name'] = 'unset';
    return ["data" => $res];
  }
  
  protected function ColorById( $id )
  {
    $res = db::Query("SELECT value as name FROM phones.model_params WHERE model=$1 AND param=102", [$id], true);
    if (!count($res))
      $res['name'] = 'unset';
    return ["data" => $res];
  }
  
  protected function StringByParam( $id )
  {
    $res = db::Query("SELECT name FROM phones.params WHERE id=$1", [$id], true);
    if (!count($res))
      $res['name'] = 'unset';
    return ["data" => $res];
  }
  
  protected function Params()
  {
    return ["data" => ["params" => db::Query("SELECT id, name FROM phones.params ORDER BY id")]];
  }
  
  protected function ModelParamValue( $phone, $param )
  {
    $res = db::Query("SELECT value as name FROM phones.model_params WHERE model=$1 AND param=$2", [$phone, $param], true);
    if (!count($res))
      $res['name'] = 'unset';
    return ["data" => $res];
  }
  
  protected function Model( $id )
  {
    $res = LoadModule('api/admin', 'editmodel')->Params();
    
    $ret = [];
    
    foreach ($res['params'] as $row)
    {
      $res = db::Query("SELECT value FROM phones.model_params WHERE model=$1 AND param=$2", [$id, $row['id']], true);

      if (!count($res))
        $res['value'] = '>=NULL=<';

      $ret[$row['id']] = $res['value'];
    }

    return [
      "design" => "admin/editmodel/body",
      "result" => "content",
      "data" => ["params" => $ret, "id" => (int)$id],
      "cache" => ["no" => "global"]
    ];
  }
  
  protected function SetParam( $phone, $param, $value )
  {
    header('Pragma: no-cache');
    $res = db::Query("SELECT id FROM phones.param_values WHERE param=$1 AND value=$2",
      [$param, $value], true);
    if (!count($res))
      $res = db::Query("INSERT INTO phones.param_values(param, value) VALUES ($1, $2) RETURNING id",
        [$param, $value], true);
        
    

    $count = db::Query("SELECT * FROM phones.model_params WHERE model=$1 AND param=$2", [$phone, $param], true);
    //var_dump([$phone, $count['id'], $res['id']]);
    if (!count($count))
      db::Query(
        "INSERT INTO phones.model_values(model, value) VALUES ($1, $2)",
        [$phone, $res['id']]);
    else
      db::Query(
        "UPDATE phones.model_values SET value=$3 WHERE model=$1 AND value=$2", 
        [$phone, $count['id'], $res['id']]);

    return ["cache" => ["no" => "global"]];
  }

  protected function Mirror( $to, $from )
  {
    $res = db::Query("SELECT * FROM phones.model_params WHERE model=$1", [$from]);
    foreach ($res as $row)
      if ($row['param'] > 100)
        $this->SetParam($to, $row['param'], $row['value']);
  }
}
