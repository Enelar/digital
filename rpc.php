<?php

error_reporting(E_ALL); ini_set('display_errors','On');

include_once('phpsql/phpsql.php');
include_once('phpsql/pgsql.php');
$sql = new phpsql();
$pg = $sql->Connect("pgsql://postgres@localhost/scladless");

include_once('phpsql/db.php');
db::Bind($pg);


function phoxy_conf()
{
  $ret = phoxy_default_conf();
  $ret["ejs_dir"] = "/ejs";
  $ret["js_dir"] = "/js";
  //if ($_GET['api'] == 'phoxy')
//    $ret["api_dir"] = "/api";
  $ret['cache_global'] = '10m';
  return $ret;
}

if (isset($_GET['api']) && $_GET['api'][0] == '!')
{
  header('HTTP/1.1 302 Found');
  header('Location: /api/' . substr($_GET['api'], 1));
  exit();
}

include('phoxy/index.php');
