<?php
$url = 'http://swapi.co/api/people/';
$fields = [ 'name' => 'John', 'surname' => 'Doe'];

$curl = curl_init($url);

// set POST method
curl_setopt($curl, CURLOPT_POST, 1);

// set POST fields; because content type is set to application/json we have to use json_encode to change representation of data
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));

curl_setopt($curl,CURLOPT_HEADER,1); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Accept: application/json'
]);


$result = curl_exec($curl);
curl_close($curl);

echo '<pre>';
var_dump($result);
?>
