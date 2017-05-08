<?php
$url = 'http://swapi.co/api/people/';

// initialize curl session, returns an instance of curl resource
$curl = curl_init();
// set url
curl_setopt($curl, CURLOPT_URL, $url);
// execute
curl_exec($curl);
// close session
curl_close($curl);
?>
