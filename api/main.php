<?php

class main extends api
{
  protected function Reserve()
  {
    return array(
      "design" => "main/body",
      "script" => ["main/loaded"],
      "routeline" => "OnDesignBoneLoads",
    );
  }
  
  protected function Home()
  {
    $catalog = LoadModule('api', 'catalog', true);
    return $catalog->Root();
  }
}
