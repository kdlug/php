# API
PHP has three basic ways of communication with API: using curl extension, pecl_http extension or stream mechanism.

## Curl (Client Url Request Library) 
In order to send a request via Curl you have to do the following 4 steps:
1. Initialization Curl session
2. Set Curl options
3. Execute request
4. Close Curl session

### GET request:

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

### cURL init
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
### Headers
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
### POST request
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

### Multiple options
To set multiple options for cURL transfer, instead of repetitively calling curl_setopt(), it's convinient to use ``bool curl_setopt_array (resource $curl , array $options)``. Function has two parameters: curl resource and options array and returnts true if all options were successfuly set, in other case it returns false.

```php
$options = [
  CURLOPT_URL => 'http://swapi.co/api/people/',
  CURLOPT_RETURNTRANSFER => 1
];

$curl = curl_init();
curl_setopt_array($curl, $options);
$result = curl_exec($curl);
curl_close($curl);

var_dump($result);
```

## pecl_http
### GET request
```php
<?php
$url = 'http://swapi.co/api/people/';

// create new request object, set url and method
$request = new HTTPRequest($url, HttpRequest::METH_GET);

//send request
$request->send();

// get result of request
$response = $request->getResponseBody();

echo '<pre>';
var_dump($response);
?>
```

### POST request
```php
<?php
$url = 'http://swapi.co/api/people/';
$fields = [ 'name' => 'John', 'surname' => 'Doe'];

$request = new HTTPRequest($url, HttpRequest::METH_POST);
$request->setPostFields($fields);

// set some headers
$request->setHeaders([
    'Content-Type: application/json',
    'Accept: application/json'  
]);

$request->send();
$response = $request->getResponseBody();

echo '<pre>';
var_dump($response);
?>
```
## Streams / file_get_contents()
file_get_contents() is used to read the contents of a file into a string. The allow_url_fopen directive is disabled by default, because of security reasons. If PHP option allow_url_fopen is set to 1, we can open remote files as if they are local files - in other words we can use url to get remote content.

### GET request
```php
<?php
// check if allow_url_fopen is enabled
$config = ini_get('allow_url_fopen');

if (!$config) {
    echo "Option allow_url_fopen is disabled";
}

$url = 'http://swapi.co/api/people/';
$result = file_get_contents($url);

echo '<pre>';
var_dump($result);
?>
```
### POST request
POST request is a little bit more complicated - we have to create a stream context first, where we define POST method and headers and content.
```php
<?php
// check if allow_url_fopen is enabled
$config = ini_get('allow_url_fopen');

if (!$config) {
    echo "Option allow_url_fopen is disabled";
}

$url = 'http://swapi.co/api/people/';
$fields = [ 'name' => 'John', 'surname' => 'Doe'];

$options = [
    'http'  => [
        'method' => 'POST',
        'header' => [
            'Accept'=>'application/json'
        ],
        'content' => http_build_query($fields)
    ]
];
// create context
$context  = stream_context_create($opts);

// pass context
$result = file_get_contents($url, false, $context);

echo '<pre>';
var_dump($result);
```
> Read more http://php.net/manual/en/function.file-get-contents.php 
