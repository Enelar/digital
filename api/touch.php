<?php

class touch extends api
{
  public function FindContact( $phone )
  {
    $res = db::Query("SELECT * FROM users.contacts WHERE phone=$1", [$phone], true);
  }
}