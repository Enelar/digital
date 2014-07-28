<?php

class buy extends api
{
  protected function RequestCallback( $phone, $id )
  {
    $id = (int)$id;
    $res = db::Query("INSERT INTO orders.callbacks(model, phone) VALUES ($1, $2) RETURNING trans",
      [$id, $phone], true);
    $ph = LoadModule('api', 'phone');
    $minimal = $ph->GetMinimalInfo($id);  
    db::Query("INSERT INTO mail.send_tasks(\"to\", subj, body, \"from\") 
    VALUES ('scladless@gmail.com', $1, $2, 'Робот Заказов <orderbot@scladless.com>')",
    ["Заказ #{$res['trans']} (перезвонить): {$minimal['name']} {$minimal['colour']} ", "Пользователь заказал {$minimal['name']} {$minimal['colour']} . Попросил перезвонить {$phone}."]);
    LoadModule('api', 'sms')->SendTo($phone, "Ваш заказ #{$res['trans']} принят. Мы перезвоним. Обязательно.");
    LoadModule('api', 'sms')->SendToManager("#{$res['trans']}({$minimal['price']}руб)\n{$phone}\n{$minimal['name']} {$minimal['colour']}");
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
    $model = (int)$model;
    $res = db::Query("INSERT INTO orders.unregistered(model, phone, email, delivery) VALUES ($1, $2, $3, $4) RETURNING id AS trans",
      [$model, $phone, $mail, $delivery], true);
    $ph = LoadModule('api', 'phone');
    $minimal = $ph->GetMinimalInfo($model);  
    db::Query("INSERT INTO mail.send_tasks(\"to\", subj, body, \"from\") 
    VALUES ('scladless@gmail.com', $1, $2, 'Робот Заказов <orderbot@scladless.com>')",
    ["Заказ #{$res['trans']} (онлайн): {$minimal['name']} {$minimal['colour']} ",
"Пользователь заказал {$minimal['name']} {$minimal['colour']} .
Оставил следующие данные:
Телефон: {$phone}
Почта: {$mail}
Доставка: {$delivery}"]);
    LoadModule('api', 'sms')->SendTo($phone, "Ваш заказ #{$res['trans']} принят. Мы перезвоним. Обязательно.");
    LoadModule('api', 'sms')->SendToManager("#{$res['trans']}({$minimal['price']}руб)\n{$phone}\n{$minimal['name']} {$minimal['colour']}\n{$delivery}");
    return ["date" => [$res]];
  }
}