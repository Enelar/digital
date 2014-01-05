<?php

error_reporting(E_ALL); ini_set('display_errors','On');

function phoxy_conf()
{
  $ret = phoxy_default_conf();
  
  return $ret;
}

include('phoxy/index.php');
