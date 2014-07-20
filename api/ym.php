<?php

include('grab/grabber.php');

class ym extends api
{
  protected function Reserve()
  {
  }
  
  protected function ModelSpec( $id )
  {
    $id = (int)$id;
    $res = exec("phantomjs --debug=no /var/www/html/api/grab/parse.js 2 $id");
    $parsed = json_decode($res, true);
    return
    [
      'design' => 'grab/model',
      'data' => $parsed,
      'cache' => ['global' => '4w'],
    ];
  }
  
  protected function Mapping()
  {
    $res = db::Query("SELECT * FROM phones.ym_mapping");
    $ret = [];
    foreach ($res as $row)
      $ret[$row['ym_name']] = $row['param'];
    return $ret;
  }

  protected function Model( $id )
  {
    $id = (int)$id;
    $res = exec("phantomjs --debug=no /var/www/html/api/grab/parse.js 0 $id");
    $parsed = json_decode($res, true);    
    return ['data' => $parsed];
  }

  private function ShouldUpdate( $id )
  {
    $res = db::Query("SELECT *, (now() - actual < '4 hours'::interval) as cur_day FROM phones.models WHERE id=$1", [$id], true);
    //var_dump($res);
    if ($res['show'] != 't')
    {
      echo "SKIP: Hide";
      return false;
    }
    if ($res['ym'] != 't')
    {
      echo "SKIP: Not shared";
      return false;
    }
    if ($res['cur_day'] == 't')
    {
      echo "SKIP: cur_day";
      return false;
    }
    return $res;
  }

  private function WarnPrice( $id, $old, $new )
  {
    if ($old == 0)
      return;
    $percent = $old / 100;
    $delta = $new - $old;
    $change = $delta / $percent;
    
    var_dump("{$id} Цена изменилась с $old на $new ($change%)");
        return;

    $change  = round($change, 1);
    if (abs($change) > 10)
      LoadModule('api', 'sms')->SendTo('+79213243303', "{$id} Price $old=>$new ($delta;$change%)");
  }

  protected function UpdatePrices( $offset = 0 )
  {
    $offset = (int)$offset;
    $reload = 10000; // 2 min is max (720 models)
    $res = db::Query("
WITH old_models AS
(
  SELECT id FROM phones.models WHERE ym=true AND now()-actual>'4 hours'::interval ORDER BY actual ASC
) SELECT model_params.* FROM phones.model_params, old_models WHERE old_models.id=model_params.model AND param=3 ORDER BY model_params.id LIMIT 10 OFFSET $offset");
    if (count($res))
      $origin = $res[0]['value'];
    else
    {
      $offset = 0;
      $reload = 60000;
    }
    for ($i = 0; $i < count($res); $i++)
    {
      $row = $res[$i];
      if ($row['value'] != $origin)
        break;
      if ($i > 0)
        continue;

    //  var_dump($row);
      $model = $this->ShouldUpdate($row['model']);
      if (!$model)
      {
        $offset++;
        $reload = 0;
        break;
      }
      $ret = $this->Grab($row['value']);
      var_dump($ret);
      if ($ret == '')
      {
        $offset++;
        $reload = 1000;
        break;
      }
      $parsed = json_decode($ret, true);
      if ($parsed == 'timeout')
      {
        $offset++;
        $reload = 1000;
        break;
      }

      if (!count($parsed['prices']))
      {
        if (!$parsed['success'])
          return;
        $offset++;
        $reload = 1000;
        break;
      }
    //  $this->ScoreProxy($proxy, 1);
      if (count($parsed['prices']) < 4)
        $price = end($parsed['prices']);
      else
        $price = $parsed['prices'][3];
      $price += 30;
      if ($price < 1000)
        LoadModule('api', 'sms')->SendTo('+79213243303', "{$row['model']} Очень низкая цена {$price} (".(json_encode($prices)).")");
      $this->WarnPrice($row['model'], $model['price'], $price);
      var_dump("PRICE: $price");
      $change = db::Query("
        UPDATE phones.models
          SET price=$2, actual=now()
          FROM phones.model_params
          WHERE models.id=model AND value=$1
          RETURNING models.id", [$row['value'], $price]);
      var_dump([$row['value'], $price]);
      var_dump($change);
      break;
      // $change;
    }
    echo "<script language='javascript'>setTimeout(function() { document.location.search='?0=$offset'}, $reload)</script>";
    return ["data" => "GRACEFUL", "cache" => ["no" => "global"]];
  }

  private function Grab( $id )
  {
    $g = LoadModule('api/grab', 'grabber');
    return $g->Grab($id);
  }
}