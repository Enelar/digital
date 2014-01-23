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

include('phoxy/index.php');
