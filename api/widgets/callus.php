<?php

class callus extends api
{
  protected function Shedule($phone)
  {
    LoadModule('api', 'sms')->SendToManager("CALLNOW {$phone}");
  }
}