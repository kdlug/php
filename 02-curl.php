<?php
$url = 'http://swapi.co/api/people/';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);

// don't print output, return it instead of true/false 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// execute and assign result to the variable
$result = curl_exec($curl);

curl_close($curl);

// print result
var_dump($result);
?>
