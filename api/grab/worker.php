<?php

class work extends Thread
{
  public $proxy;
  private $model;
  public $type;
  public $res;

  public function __construct($proxy, $model, $type)
  {
    $this->proxy = $proxy;
    $this->model = $model;
    $this->type = $type;
    $this->res = "test";
  }

  public function run()
  {
    //$query = "phantomjs --proxy={$this->proxy} --debug=no /var/www/html/api/grab/parse.js 0 {$this->model}";
    $query = "phantomjs --debug=no  /var/www/html/api/grab/parse.js  0 {$this->model}";
    var_dump($query);
    $this->res = //exec("pwd");
    exec($query);
  }
}