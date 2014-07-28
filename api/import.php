<?php

class import extends api
{
  protected function Reserve()
  {
  }
  
  protected function FromPrice( $name, $color )
  {
    $res = db::Query("INSERT INTO phones.models(price) VALUES (0) RETURNING id", [], true);
    phoxy_protected_assert($res, ["error" => "Model not inserted"]);
    $model_id = $res['id'];

    $edit_model = LoadModule('api/admin', 'editmodel');
    $edit_model->SetParam($model_id, 1 /* name */, $name);
    $edit_model->SetParam($model_id, 102 /* color */, $color);

    return $model_id;
  }
}