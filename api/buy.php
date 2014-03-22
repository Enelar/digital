<?php

class buy extends api
{
  protected function RequestCallback( $phone, $id )
  {
    $res = db::Query("INSERT INTO orders.callbacks(model, phone) VALUES ($1, $2) RETURNING trans",
      [$id, $phone], true);
    return ["date" => [$res]];
  }
  
  protected function Registered( $id )
  {
    $login = LoadModule('api', 'login');
    $res = db::Query("INSERT INTO orders.registered(phone, uid) VALUES ($1, $2) RETURNING trans",
      [$id, $login->UID()], true);
    return ["date" => [$res]];
  }
}