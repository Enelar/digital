<?php

class slack extends api
{
  private function GetToken( )
  {
    return 'UdfpmSNZhZTorVRpbLgFrmEW';
  }

  private function WriteTo( $channel, $message )
  {
    return $this->WriteTo(['channel' => $channel, 'text' => $message]);
  }

  private function SendObj( $obj )
  {
    $token = $this->GetToken();
    $url = "https://scladless.slack.com/services/hooks/incoming-webhook?token={$token}";

    $post =
    [
      "payload" => json_encode($obj),
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);

    curl_close ($ch);
    $obj = json_decode($server_output, true);
    return $obj;
  }

  protected function Test()
  {
    return $this->WriteTo("orders", "TEST");
  }

  public function Order( $id, $modelname, $price, $callback, $email = "", $delivery = "" )
  {
    $this->SendObj(
    [
      "channel" => "#orders",
      "username" => "OrderBot",
      "text" => "#{$id}, {$modelname}({$price}руб)\n{$callback} {$email}\n{$delivery}",
    ]);
  }
}