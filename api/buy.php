<?php

class buy extends api
{
  protected function RequestCallback( $phone, $id )
  {
    $res = db::Query("INSERT INTO orders.callbacks(model, phone) VALUES ($1, $2) RETURNING trans",
      [$id, $phone], true);
    $ph = LoadModule('api', 'phone');
    $minimal = $ph->GetMinimalInfo($id);  
    db::Query("INSERT INTO mail.send_tasks(\"to\", subj, body, \"from\") 
    VALUES ('ks78@inbox.ru, info@digital812.ru, digital812.shop@gmail.com', $1, $2, 'Робот Заказов <orderbot@digital812.ru>')",
    ["Заказ #{$res['trans']} (перезвонить): {$minimal['name']}", "Пользователь заказал {$minimal['name']}. Попросил перезвонить {$phone}."]);
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