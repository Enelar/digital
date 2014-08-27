<?php

function ECCO( $hash )
{
  echo "http://scladless.com/?_escaped_fragment_={$hash}\n";
}

class sitemap extends api
{
  protected function Reserve()
  {
    ECCO('delivery');
    ECCO('contacts');
    $vendors = LoadModule('api', 'catalog')->Root();
    $pclass = LoadModule('api/catalog', '_phone');

    foreach ($vendors['vendors'] as $row)
    {
      $res = $pclass->Vendor($row['name']);
      foreach ($res['vendor'] as $p)
        ECCO("catalog/{$row['name']}/{$p['id']}/{$p['name']} {$p['colour']}\n");
    }
    
    exit();
  }
}