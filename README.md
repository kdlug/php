# API
PHP has three basic ways of communication with API: using curl extension, pecl_http extension or stream mechanism.

## Curl (Client Url Request Library) 
In order to send a request via Curl you have to do the following 4 steps:
1. Initialization Curl session
2. Set Curl options
3. Execute request
4. Close Curl session

Basic GET request:

```php
$url = 'http://swapi.co/api/people/';

// initialize curl session, returns an instance of curl resource
$curl = curl_init();
// set url
curl_setopt($curl, CURLOPT_URL, $url);
// execute
curl_exec($curl);
// close session
curl_close($curl);
```
The default value returned by curl_exec() is true/false and the response will be printed to the standard output. We can change this behaviour of curl_exec and instead of printing response we can return result to the variable. To do that we need add an option CURLOPT_RETURNTRANSFER and set it's value to 1.

```php
$url = 'http://swapi.co/api/people/';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);

// don't print output, return it instead of true/false 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// execute and assign result to the variable
$result = curl_exec($curl);

curl_close($curl);

// print result
var_dump($result);
```
Function curl_init has one optional parameter - if you pass a string to it it will be automatically used as URL address. It's equivalent to `curl_setopt($curl, CURLOPT_URL, $url)`.
```php
$url = 'http://swapi.co/api/people/';

// initialize curl session and pass url
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);
curl_close($curl);

var_dump($result);
```
Header Content-Type is used to determine data format of request body. Thanks to it the recipient knows how to decode received content. The similar header Accept is used to determine by client what kind of content is accepted for the response. To include headers in response you can set CURLOPT_HEADER option to 1. Using cURL we can simply add these headers to the request:
```php
$url = 'http://swapi.co/api/people/';

$curl = curl_init($url);

// include headers in response
curl_setopt($curl, CURLOPT_HEADER, 1); 

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// set headers in array
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Accept: text/html'
]);

$result = curl_exec($curl);
curl_close($curl);
var_dump($result);
```
To send a simple POST request we need to set 2 options: 
- CURLOPT_POST with value 1 
- CURLOPT_POSTFIELDS with an array of fields which have to be send

```php
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
```
Above example will return 405 METHOD NOT ALLOWED because POST method for this specific API is forbidden.

Function curl_setopt has more options - you can check all options here: https://curl.haxx.se/libcurl/c/curl_easy_setopt.html.
