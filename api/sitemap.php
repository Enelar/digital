<?php

function ECCO( $hash )
{
  echo "http://scladless.com/?_escaped_fragment_={$hash}\n";
}

function XECCO( $root, $hash, $freq = "daily" )
{
  $url = $root->createElement('url');
  $url->createElement('loc')->createTextNode("http://scladless.com/?_escaped_fragment_={$hash}");;
  $url->createElement('changefreq')->createTextNode($freq);
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
        ECCO("catalog/{$row['name']}/{$p['id']}/{$p['name']} {$p['colour']}");
    }
    
    exit();
  }

  protected function Better()
  {
    date_default_timezone_set('Europe/Moscow');
    header("Content-Type: text/xml; charset=utf-8");

    include('xml/XMLHelper.php');
    $dom = new XMLHelper('1.0', 'utf-8');
    $root = $dom->createElement('urlset');
    $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    XECCO($root, 'delivery', 'weekly');
    XECCO($root, 'contacts', 'weekly');

    $vendors = LoadModule('api', 'catalog')->Root();
    $pclass = LoadModule('api/catalog', '_phone');

    foreach ($vendors['vendors'] as $row)
    {
      $res = $pclass->Vendor($row['name']);
      foreach ($res['vendor'] as $p)
        XECCO($root, "catalog/{$row['name']}/{$p['id']}/{$p['name']} {$p['colour']}");
    }

    die($dom);
  }
}