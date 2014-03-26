<?php

class editmodel extends api
{
  protected function Reserve()
  {
	header('Pragma: no-cache');
    $res = db::Query("SELECT id FROM phones.models ORDER BY id ASC");
    $ret = [];
    foreach ($res as $row)
      array_push($ret, $row['id']);
    return [
      "design" => "admin/editmodel/list",
      "result" => "content",
      "data" => ["models" => $ret]
    ];
  }
  
  protected function ModelById( $id )
  {
    $res = db::Query("SELECT value as name FROM phones.model_params WHERE model=$1 AND param=171", [$id], true);
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
	return ["data" => ["params" => db::Query("SELECT id, name FROM phones.params WHERE name != 'picture' ORDER BY id")]];
  }
  
  public function ModelParamValue( $phone, $param )
  {
	header('Pragma: no-cache');
	return ["data" => ["value" => "NULL"]];
  }
  
  protected function Model( $id )
  {
	header('Pragma: no-cache');
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
      "data" => ["params" => $ret, "id" => (int)$id]
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
	if (!count(db::Query("SELECT * FROM phones.model_params WHERE model=$1 AND param=$2", [$phone, $param], true)))
		$query = "INSERT INTO phones.model_values(model, value) VALUES ($1, $2)";
	else
		$query = "UPDATE phones.model_values SET value=$2 WHERE model=$1";
	db::Query($query, [$phone, $res['id']]);
  }
}
