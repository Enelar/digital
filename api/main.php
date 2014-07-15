<?php

class main extends api
{
  protected function Reserve()
  {
    return array(
      "design" => "main/body",
      "script" => ["main/loaded"],
      "before" => "OnDesignBoneLoads",
    );
  }
  
  protected function Home()
  {
    $catalog = LoadModule('api', 'news', true);
    return $catalog->Reserve();
  }
}
