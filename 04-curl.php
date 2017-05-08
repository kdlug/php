<?php
$url = 'http://swapi.co/api/people/';

$curl = curl_init($url);

// include headers in response
curl_setopt($curl,CURLOPT_HEADER,1); 
// save response to variable instead to stdout 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// set headers in array
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Accept: text/html'
]);

$result = curl_exec($curl);
curl_close($curl);

echo '<pre>';
var_dump($result);
?>
