<?php

if ($_SERVER['REDIRECT_URL'] != '/')
  $url = $_SERVER['REDIRECT_URL'];
else
  $url = $_GET['_escaped_fragment_'];

if (strpos($url, '"') !== false)
  die("Security bless you");

$result = tempnam("/tmp", "googleit_");

$args =
[
  escapeshellarg($result), 
  escapeshellarg("http://scladless.com/#$url"),
];
$query = 'phantomjs googleit.js '.(implode(' ', $args));

if ($_SERVER['REMOTE_ADDR'] == '213.21.7.6')
  $res = system($query);
else
  $res = exec($query);

$return = file_get_contents($result);
echo $return;

unlink($result);

if ($res == false)
  echo "Generation error";