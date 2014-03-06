<?php

class storage extends api
{
  public function __construct()
  {
    $this->addons = 
      array(
        "cache" => 
          array("no" => "global")
      );
    parent::__construct();
  }
  
  protected function Reserve()
  {
    return array(
      "design" => "admin/storage",
      "result" => "content"
    );
  }
  
  protected function SearchByIMEI($q)
  {
    $lot = db::Query("SELECT * FROM storage.lot WHERE imei=$1", array($q), true);    

    $data = array(
      'known' => count($lot) > 0,
      'lot' => $lot,
    );
    
    $ret = array(
      "script" => "admin/storage",
      "routeline" => "SearchByIMEI",
      "data" => $data
    );
    
    if ($data['known'])
    {
      $ret['design'] = 'admin/storage_lot_admin';
      $ret['result'] = 'admin_place';
    }
    
    return $ret;
  }
  
  protected function SearchByModel($q)
  {
    $barcode = db::Query("SELECT * FROM storage.barcodes WHERE barcode=$1", array($q), true);

    $data = array(
      'known' => count($barcode) > 0,
    );
    
    $ret = array(
      "script" => "admin/storage",
      "routeline" => "SearchByModel",
    );
    
    if ($data['known'])
    {
      $data['model'] = $barcode['model'];
      $data['barcode'] = $barcode['barcode'];    
    
      $ret['design'] = 'admin/storage_model_admin';
      $ret['result'] = 'admin_place';

      $count = db::Query("SELECT count(*) FROM storage.lot WHERE status != 10 AND status != 3 AND model=$1", array($q), true);
      $data['count'] = $count['count'];
    }
    
    $ret["data"] = $data;

    return $ret;
  }
  
  protected function BindImeiAndModel( $imei, $model )
  {
    $arr = db::Query("INSERT INTO storage.lot (imei, model) VALUES ($1, $2) RETURNING id", array($imei, $model), true);
    phoxy_protected_assert(count($arr) > 0, array("error" => "Something went wrong"));
    return array(
      "script" => "admin/storage",
      "routeline" => "BindImeiAndModel",
      "data" => array(
        "binded" => true,
        "id" => $arr['id']
      )
    );
  }
  
  protected function BindBarcodeAndModel( $barcode, $model )
  {
    $arr = db::Query("INSERT INTO storage.barcodes (model, barcode) VALUES ($1, $2) RETURNING model", array($model, $barcode), true);
    phoxy_protected_assert(count($arr) > 0, array("error" => "Something went wrong"));
    return array(
      "script" => "admin/storage",
      "routeline" => "BindBarcodeAndModel",
      "data" => array(
        "binded" => true
      )
    );
  }
  
  protected function GetModelName($id)
  {
    $res = db::Query("
    WITH name_param AS
    (
      SELECT id FROM phones.params WHERE name='name' LIMIT 1
    ), modelid AS
    (
      SELECT * FROM storage.barcodes WHERE barcode=$1 LIMIT 1
    )
    SELECT
      * 
    FROM
      phones.model_params
    WHERE 
      param =
        (SELECT id FROM name_param)
      AND
      model = (SELECT model FROM modelid)
    ", array($id), true);
    
    return array(
      "data" => array("name" => $res['value']),
      "cache" => array("global" => "1d")
    );
  }
  
  protected function GetStatusName($id)
  {
    $res = db::Query("SELECT name FROM storage.statuses WHERE id=$1", array($id), true);
    return array(
      "data" => array("status" => $res['name']),
      "cache" => array("global" => "1d")
    );    
  }
  
  protected function GetUnbindedModelList()
  {
    $arr = db::Query("
    WITH known_barcodes AS
    (
      SELECT model FROM storage.barcodes
    ),
    unbinded_models AS
    (
      SELECT * FROM phones.models WHERE NOT (id = ANY (SELECT * FROM known_barcodes))
    ), name_param AS
    (
      SELECT id FROM phones.params WHERE name='name' LIMIT 1
    )
    SELECT
      phones.model_params.* 
    FROM
      phones.model_params,
      unbinded_models
    WHERE 
      phones.model_params.param =
        (SELECT id FROM name_param) 
     AND
      phones.model_params.model =
      unbinded_models.id
    ORDER BY
      value ASC
      ");
    $ret = array();
    foreach ($arr as $row)
      array_push($ret, array('id' => $row['model'], 'name' => $row['value']));
    return array(
      "script" => "bootstrap-select",
      "data" => array(
        "list" => $ret
      )
    );
  }
  
  protected function GetLotLog( $id )
  {
    $arr = db::Query("SELECT * FROM storage.lot_log WHERE imei=$1 ORDER BY snap DESC", array($id));
    return array("data" => array("log" => $arr));
  }
  
  protected function UpdateStatus( $imei, $status, $price = NULL )
  {
    db::Query("UPDATE storage.lot SET status=$2, price=$3 WHERE imei=$1", array($imei, $status, $price), true);
  }
  
  protected function CreateModel( $barcode, $name )
  {
    $name = db::Query("
    WITH name_param AS
    (
      SELECT id FROM phones.params WHERE name='name' LIMIT 1
    ) INSERT INTO phones.param_values(param, value) VALUES ((SELECT id FROM name_param), $1) RETURNING id",
    array($name), true);
    
    $model = db::Query("INSERT INTO phones.models(price) VALUES (0) RETURNING id", array(), true);
    $attr = db::Query("INSERT INTO phones.model_values(model, value) VALUES ($1, $2)", array($model['id'], $name['id']));
    
    $bind = db::Query("INSERT INTO storage.barcodes(model, barcode) VALUES ($1, $2)", array($model['id'], $barcode));
    
    return array(
      'data' => array('model' => $model['id'])
    );
  }
}
