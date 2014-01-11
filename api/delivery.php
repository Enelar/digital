<?php

class delivery extends api
{
  protected function Reserve()
  {
    $prices = LoadModule('api', 'delivery');
    return array
    (
      "design" => "pages/delivery",
      "result" => "content",
      "data" => $prices->Prices()
    );
  }
  
  protected function Prices()
  {
    return array
    (
      "design" => "pages/delivery_prices",
      "data" => array
      (
        "prices" => array
        (
          array
          (
            "title" => "В пределах КАД",
            "price" => 250,
            "timing" => "В день заказа/на следующий день"
          ),
          array
          (
            "title" => "Приоритетная",
            "price" => 500,
            "timing" => "В течении 6 часов"
          ),          
          array
          (
            "title" => "Пригород 20КМ",
            "price" => 500,
            "timing" => "На следующий день"
          ),
        )
      )    
    );
  }
  
}
