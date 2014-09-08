<?php
date_default_timezone_set('Europe/Moscow');
header("Content-Type: text/xml; charset=utf-8");

class xml extends api
{
  protected function Reserve()
  {
    include('xml/XMLHelper.php');
    $dom = new XMLHelper('1.0', 'utf-8');
        
    $root = $dom->createElement('yml_catalog');
    $root->setAttribute('date', date('o-m-d H:i'));
    
    $shop = $root->createElement('shop');
    $shop->createElement('name')->createTextNode('scladless.com');
    $shop->createElement('company')->createTextNode('scladless.com');
    $shop->createElement('url')->createTextNode(phoxy_conf()['site']);
    
    $currencies = $shop->createElement('currencies');
    $currencies->createElement('currency')->setAttribute('id', 'RUR')->setAttribute('rate', '1');
    
    $categories = $shop->createElement('categories');
    $cat = $categories->createElement('category');
    $cat->setAttribute('id', 1)->createTextNode('Мобильные телефоны');
/*    
<categories>
<category id="1">Мобильные телефоны</category>
<category id="7">Планшеты</category>
<category id="22">Медиа плееры</category>
<category id="8">Аксессуары</category>
<category id="18">Уцененный товар</category>
</categories>  */  

    $shop->createElement('local_delivery_cost')->createTextNode(250);
    
    $offers = $shop->createElement('offers');
    $this->Offers($offers);
    
    die($dom);
  }
  
  private function Offers($root)
  {
    $ph = LoadModule('api', 'phone');
    $phones = db::Query("
      SELECT *
        FROM phones.models
        WHERE 
          vendor IN (SELECT id FROM phones.vendor WHERE hide=false)
          AND show = true
          AND now() - actual < '48 hour'::interval");
    
    foreach ($phones as $phone)
    {
      $vendor = $ph->GetVendor($phone['id'])['name'];
      $minimal = $ph->GetMinimalInfo($phone['id']);      
      
      $offer = $root->createElement('offer');
      $offer->setAttribute('id', $phone['id']);
      $offer->setAttribute('type', 'vendor.model');
      $offer->setAttribute('available', $phone['quantity'] > 0 ? 'true' : 'false');
      
      $url = phoxy_conf()['site']."?utm_source=yandex_market&utm_medium=cpc&utm_campaign=default#!catalog/{$vendor}/{$phone['id']}/".(urlencode($minimal['name'].' '.$minimal['colour']));
      $offer->createElement('url')->createTextNode($url);
      $offer->createElement('price')->createTextNode($phone['price']);
      
      $offer->createElement('currencyId')->createTextNode('RUR');
      $offer->createElement('categoryId')->createTextNode('1');

      $offer->createElement('picture')->createTextNode((phoxy_conf()['site'])."{$minimal['picture']}");
      
      //$offer->createElement('store')->createTextNode('true');
      //$offer->createElement('pickup')->createTextNode('true');
      $offer->createElement('delivery')->createTextNode('true');

      $offer->createElement('vendor')->createTextNode("{$vendor}");
      $offer->createElement('model')->createTextNode("{$minimal['name']} {$minimal['colour']}");
      
      $offer->createElement('description');
    }
  }
}