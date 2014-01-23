<?php

class login extends api
{
  protected function Form()
  {
    return array(
      "design" => "login/form",
      "script" => "js/main/login.js"
    );
  }
}