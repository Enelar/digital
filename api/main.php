<?php

class main extends api
{
  protected function Reserve()
  {
    return array(
      "design" => "main/body",
      "script" => ["main/loaded", "url-hook"],
      "before" => "OnDesignBoneLoads",
    );
  }
  
  protected function Home()
  {
    $catalog = LoadModule('api', 'news', true);
    return $catalog->Reserve();
  }

  protected function OptOut()
  {
    return
    [
      "design" => "main/optout",
      "script" => "ouibounce",
    ];
  }
  protected function OptOutFeedback( )
  {
    global $_POST;
    $ip = phoxy_conf()['ip'];
    $res = db::Query("INSERT INTO optout_feedback(ip,mess,phone) VALUES($1,$2,$3) RETURNING snap",
      [
        $ip,
        $_POST['mess'],
        $_POST['phone']
      ]);
    //LoadModule('api', 'sms')->SendTo('+79213243303', "OptOutFeedback");
    return $res['snap'];
  }
}
