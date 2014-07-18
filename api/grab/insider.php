<?php

class insider extends api
{
  protected function Reserve()
  {
    return
    [
      "design" => "grab/entry",
      "result" => "content",
    ];
  }

  protected function ShowMe()
  {
    $id = 10500049;
    $query = "phantomjs --debug=no /var/www/html/api/grab/parse.js 0 {$id}";
    return
    [
      "design" => "grab/show",
      "data" => json_decode(exec($query), true),
    ];
  }

  protected function Capcha( $a, $b, $c )
  {
    $a = rawurlencode($a);
    $b = rawurlencode($b);
    $c = rawurlencode($c);

    $q = "phantomjs --debug=no /var/www/html/api/grab/capcha.js ";
    $u = "http://market.yandex.ru/checkcaptcha?";
    $u .= "key=$a&retpath=$b&rep=$c";
    $q .= escapeshellarg($u);

    echo $q;
    echo "<br>";
    echo "ANSWER[";
    echo system($q);
    echo "]ANSWER";
  }
}