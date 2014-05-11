<?php

$url = $_GET['_escaped_fragment_'];
if (strpos($url, '"') !== false)
  die("Security bless you");

$res = system('phantomjs /opt/googleit.js '.escapeshellarg("http://scladless.com/#.$url."));

if ($res == false)
  echo "Generation error";
else
  echo $res;