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
    VALUES ('scladless@gmail.com', $1, $2, 'Робот Заказов <orderbot@scladless.com>')",
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
  
  protected function Unregistered( $model, $phone, $mail, $delivery )
  {
    $res = db::Query("INSERT INTO orders.unregistered(model, phone, email, delivery) VALUES ($1, $2, $3, $4) RETURNING id AS trans",
      [$model, $phone, $mail, $delivery], true);
    $ph = LoadModule('api', 'phone');
    $minimal = $ph->GetMinimalInfo($model);  
    db::Query("INSERT INTO mail.send_tasks(\"to\", subj, body, \"from\") 
    VALUES ('scladless@gmail.com', $1, $2, 'Робот Заказов <orderbot@scladless.com>')",
    ["Заказ #{$res['trans']} (онлайн): {$minimal['name']}",
"Пользователь заказал {$minimal['name']}.
Оставил следующие данные:
Телефон: {$phone}
Почта: {$mail}
Доставка: {$delivery}"]);      
    return ["date" => [$res]];
  }
}