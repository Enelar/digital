<?php

class updateprice extends api
{

  private function UpdateModel($id, $price)
  {
    $ch = curl_init();

    $post =
    [
      "method" => "actionUpdateAttribute",
      "arguments[productId]" => $id,
      "arguments[attribute]" => "price",
      "arguments[value]" => $price,
    ];

    var_dump($post);

    curl_setopt($ch, CURLOPT_URL, "http://www.digital812.ru//yandex/v3/api/init.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec ($ch);
    curl_close ($ch);

    return $server_output;
  }

  protected function Next()
  {
    $res = db::Query("SELECT * FROM market.digital_bind WHERE last_sync IS NULL OR now() - last_sync > '2 hours'::interval ORDER BY last_sync ASC LIMIT 1", [], true);
    var_dump($res);
    return $res;
  }

  private function RoundPrice( $a, $b )
  {
    if ($a == $b)
      return (int)($a / 10) * 10;
    $c = ($a + $b) / 2; // взять середину
    $c = (int)($c / 10) * 10; // округлить по 10
    return $c;
  }

  protected function GetPrice()
  {
    $model = $this->Next();
    if (!$model)
      return false;

    $ymid = $model['ymid'];

    $tell = LoadModule('api/ym/sentinel', 'tell');
    $price = $tell->LastPrice($ymid);

    $p = $price['price'];

    for ($i = 0; $i < count($p); $i++)
      echo "$p[$i]: {$price['shop'][$i]}<br>";

    if (count($p) > 2)
      $this->UpdateModel($model['id'], $this->RoundPrice($p[1], $p[2]));
    else if (count($p) > 0)
      $this->UpdateModel($model['id'], $p[0]);
    else
      return false;

    db::Query("UPDATE market.digital_bind SET last_sync = now() WHERE id=$1", [$model['id']]);
    echo "<script language='javascript'>setTimeout(function() { document.location.search='?'}, 50)</script>";
    return true;
  }
}