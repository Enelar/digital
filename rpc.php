<?php

error_reporting(E_ALL); ini_set('display_errors','On');

include_once('pgsql_php/connect.php');
new db("dbname=digital host=localhost user=postgres");

function phoxy_conf()
{
  $ret = phoxy_default_conf();
  $ret['cache_global'] = '10m';
  return $ret;
}

if (isset($_GET['api']) && $_GET['api'][0] == '!')
{
  header('HTTP/1.1 302 Found');
  header('Location: http://scladless.com/api/' . substr($_GET['api'], 1));
  exit();
}

include('phoxy/index.php');
