<?php

class storage extends api
{
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

    $ret = array(
      'known' => count($lot) > 0,
      'lot' => $lot,
    );
    
    return array(
      "script" => "admin/storage",
      "routeline" => "SearchByIMEI",
      "data" => $ret
    );
  }
  
  protected function SearchByModel($q)
  {
    $barcode = db::Query("SELECT * FROM storage.barcodes WHERE barcode=$1", array($q), true);
    $ret = array(
      'known' => count($barcode) > 0,
      'barcode' => $barcode
    );
    
    return array(
      "script" => "admin/storage",
      "routeline" => "SearchByModel",
      "data" => $ret
    );
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
}
