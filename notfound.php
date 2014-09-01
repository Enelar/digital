<?php

function ExceptionParams( &$arr )
{
  $ret = [];
  $words =
  [
    "utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content", // google analystics
  ];

  foreach ($arr as $word => $val)
  {
    if (!in_array($word, $words))
      continue;
    unset($arr[$word]);
    $ret[$word] = $val;
  }
  return $ret;
}

$url = $_GET['url'];
unset($_GET['url']);
$exception_query = http_build_query(ExceptionParams($_GET));
$query = http_build_query($_GET);
if ($query != "")
  $url .= "?{$query}";

// Business code here
if ($url[0] == '~')
  $url = "catalog/phones/".substr($url, 1);

// End of business code
if ($exception_query != "")
  $out = "/?{$exception_query}";
else
  $out = "/";

header('HTTP/1.1 302 Found');
header("Location: {$out}#{$url}");