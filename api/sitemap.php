<?php

class sitemap extends api
{
  protected function Reserve()
  {
?>
http://scladless.com/#!delivery
http://scladless.com/#!contacts
<?php
    $vendors = LoadModule('api', 'catalog')->Root();
    $pclass = LoadModule('api/catalog', '_phone');

    foreach ($vendors['vendors'] as $row)
    {
      $res = $pclass->Vendor($row['name']);
      foreach ($res['vendor'] as $p)
        echo "http://scladless.com/#!catalog/{$row['name']}/{$p['id']}/{$p['name']} {$p['colour']}\n";
    }
    
    exit();
  }
}