# API

## Sending HTTP requests using PHP
PHP has three basic ways of communication with API: using curl, pecl_http or file_get_content().
### Curl (Client Url Request Library) 
In order to send a request via Curl you have to do the following 4 steps:
1. Initialization Curl session
2. Set Curl options
3. Execute request
4. Close Curl session

#### GET request:

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

#### cURL init
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
#### Headers
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
#### POST request
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

#### Multiple options
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

### pecl_http
#### GET request
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

#### POST request
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
### file_get_contents()
file_get_contents() is used to read the contents of a file into a string. The allow_url_fopen directive is disabled by default, because of security reasons. If PHP option allow_url_fopen is set to 1, we can open remote files as if they are local files - in other words we can use url to get remote content.

#### GET request
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
#### POST request
POST request is a little bit more complicated - we have to create a stream context first, where we define POST method and headers and content.
```php
<?php
// check if allow_url_fopen is enabled
$config = ini_get('allow_url_fopen');

if (!$config) {
    echo "Option allow_url_fopen is disabled";
}

$url = 'http://requestb.in/1hq5frz1';
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

## HTTP Methods
In PHP we can determine which method was used in a request using superglobal `$_SERVER['REQUEST_METHOD']`.

### GET
GET requests sends query string (key/value pairs) in the URL: 
f.ex. http://example.comt/form.php?key1=value1&key2=value2

A few notes about GET requests:
- can be cached
- remain in the browser history
- can be bookmarked
- should never be used when dealing with sensitive data
- have length restrictions
- should be used only to get data from the server (search)

In PHP there is superglobal variable `$_GET` which contains GET requests.

#### Send querystring via GET using cURL
```php
$url = 'http://requestb.in/1hq5frz1';
$data = [
  'key1' => 'value1',
  'key2' => 'value2'
];
$address = $url . '?' . http_build_query($data);

$curl = curl_init($address);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);
curl_close($curl);

// print result
var_dump($result);
```
We can check query string on the requestb page, it looks like this:
```
QUERYSTRING
key2: value2
key1: value1
```
#### Send querystring via GET using file_get_contents
```php
<?php
$url = 'http://requestb.in/1hq5frz1';
$data = [
  'key1' => 'value1',
  'key2' => 'value2'
];

$address = $url . '?' . http_build_query($data);

$result = file_get_contents($address);

echo '<pre>';
var_dump($result);
?>
```
### POST
In POST requests data is sent in content/body of a HTTP request (not in the URL like in GET) and Content-Type header determines type of sent data. 
Example od the post request was mentioned here: https://github.com/kdlug/php/blob/master/README.md#post-request
Content of this request looks like the following
```
RAW BODY
{"name":"John","surname":"Doe"}
```
A few notes about POST requests:
- never cached
- don't remain in the browser history
- cannot be bookmarked
- no restrictions on data length
- used for changing data on the server f.ex. inserting / updating object

In PHP there is superglobal variable `$_POST` which contains POST requests.
### DELETE
Used for deleting objects on the server.

### PUT
Similar to POST, becase it's requests can contain data in various formats. 

#### Retreive PUT request
In php there is no superglobal for retreive PUT data - do get data from PUT we can use stream php://input.
```php
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = [];
    // read raw POST data / query string
    $raw = file_get_contents("php://input");
    // encode query string to $data array
    parse_str($raw, $data);
}
```
> Read more http://php.net/manual/en/function.parse-str.php

#### Send PUT request via cURL
```php
$url = 'http://requestb.in/1hq5frz1';

$curl = curl_init($url);

// set HTTP method to DELETE
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
$result = curl_exec($curl);
curl_close($curl);
// print result
var_dump($result);
```
#### Send PUT request via pecl_http
#### Send PUT request via file_get_contents

## HTTP Headers
HTTP header fields provide information about the request or response, or about the object sent in the message body.
Header fields are colon-separated name-value pairs in text format, terminated by a carriage return (CR) and line feed (LF) character sequence. The end of the header section is indicated by an empty field. 
A lot of headers occur both in requests and responses (entity headers), but some of them are specific only for requests or responses. 

Example response headers

```curl -I https://time.com/```
```
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
Connection: keep-alive
Server: nginx
Date: Sat, 20 May 2017 18:03:17 GMT
Vary: Accept-Encoding
Vary: Cookie
X-hacker: If you're reading this, you should visit automattic.com/jobs and apply to join the fun, mention this header.
X-UA-Compatible: IE=edge,chrome=1
Link: <http://ti.me/nACNOw>; rel=shortlink
X-ac: 4.fra _dfw
X-Cache: Miss from cloudfront
Via: 1.1 66ee7af4768b1b41e7f77d2e5b20df5c.cloudfront.net (CloudFront)
X-Amz-Cf-Id: Iu496Id5-2eJJkRYoMrPqVCToYzuJvEtlCvLLcwb_AeDxKdZQ91LVA
```
Example request headers
```
GET / HTTP/1.1
Accept-Encoding: gzip
Host: requestb.in
Accept: text/html, application/xhtml+xml, image/jxr, */*
Accept-Language: pl
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393
Via: 1.1 vegur
```
 
### Most used headers
#### User Agent
Shows information about the client which sent a request.
F.ex.:
```
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36
```
We shouldn't trust User Agent header, because it can be simply modified via curl:
```
curl -H 'User-Agent: Custom Client' https://requestb.in/zx5pgqzx
```
In PHP we can set User-Agent header like the following:
```php
$url = 'https://requestb.in/zx5pgqzx';

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_HEADER, 1); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// set headers in array
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'User-Agent: Sample custom header'
]);

$result = curl_exec($curl);
curl_close($curl);
var_dump($result);
```

All headers in PHP we can find in $_SERVER array. User-Agent header can be checked in $_SERVER['HTTP_USER_AGENT']

#### Content Type
The Content-Type entity header field indicates format (media type) of data sent/returned in requests (POST, PUT)/responses.

#### Accept
Request header which can be used by the client to specify expected format of data for the response.
For instance Chrome browser can handle the following media types: 
```Accept: text/html, application/xhtml+xml, image/jxr, */*```
If response will be returned in one of above formats, client can regognize it. */* at the end means that Chrome browser supposedly
can handle every type of content.
There are a few headers which, similar to accept, are used for content negotiation: Accept-Charset, Accept-Encoding, Accept-Language

Example PHP classes for processing request header can be found here: 
https://github.com/adoy/Accept-Header-Parser/blob/master/AcceptHeader.php 
https://github.com/ramsey/mimeparse-php/blob/master/src/Mimeparse.php. 
A nice article about the importance of Accept header analysis: http://shiflett.org/blog/2011/may/the-accept-header. 

Setting Accept header via curl:
```
curl -H "Accept: text/html;q=0.1,application-json" http://example.com
```

PHP:
```php
$url = 'https://requestb.in/zx5pgqzx';

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_HEADER, 1); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// set headers in array
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'Accept: text/html;q=0.1,application-json'
]);

$result = curl_exec($curl);
curl_close($curl);
var_dump($result);
```
It's a good practice to prepare API which serves a few types of content f.ex. JSON and XML.


#### Authorization
Basic / OAuth

## Cookies

## Authentication
Cookies / JWT
https://auth0.com/blog/cookies-vs-tokens-definitive-guide/

## XML / JSON format
## RPC / SOAP
## REST
## Error Handling
## Debugging
## Documentation
https://any-api.com/
