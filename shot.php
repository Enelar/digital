<?php

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

$res = exec($query);

$return = file_get_contents($result);
echo $return;

unlink($result);

if ($res == false)
  echo "Generation error";