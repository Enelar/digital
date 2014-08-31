<?php

$url = $_GET['url'];
if ($url[0] == '~')
  $url = "catalog/phones/".substr($url, 1);

header('HTTP/1.1 302 Found');
header('Location: /#'.$url);