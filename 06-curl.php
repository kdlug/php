<?php
$options = [
    CURLOPT_URL            => 'http://swapi.co/api/people/',
    CURLOPT_RETURNTRANSFER => 1
];

$curl = curl_init();
curl_setopt_array($curl, $options);
$result = curl_exec($curl);
curl_close($curl);

echo '<pre>';
var_dump($result);
?>
