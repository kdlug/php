<?php
$url = 'http://swapi.co/api/people/';

// initialize curl session and pass url
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
curl_close($curl);

// print result
var_dump($result);
?>
