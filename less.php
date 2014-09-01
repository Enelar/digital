<?php

$url = $_GET['file'];
if (strpos($url, '"') !== false)
  die("Security bless you");

$result = tempnam("/tmp", "googleit_");

$query = 'lessc '.escapeshellarg("{$url}.less");

header("Content-type: text/css");
$res = system($query);

if ($res == false)
  echo "Generation error";