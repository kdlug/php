# API
## HTTP message
Each request/response message consists of:
- request line
- headers
- an empty line
- message body (optional)
```sh
GET / HTTP/1.1     ==> request line
Host: example.com  ==> headers
                   ==> empty line <CR><LF>, without other whitespaces            
message body       ==> body
```
### Request message
The simpliest GET request can look like this:
```sh
GET / HTTP/1.1
Host: example.com
```
The first line of the HTTP request is called the request line and consists of 3 parts:
- request method: GET (can be POST, PUT, DELETE etc.)
- path: /
- protocol: HTTP/1.1

The request contains HTTP headers as "Name: Value" pairs in each line. Most of headers are optional, except Host header, which defines address of the resource:
- Host: example.com

### Response message
```php
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
Content-Encoding: UTF-8
Content-Length: 12
Connection: close

Message body
```
The first line of the HTTP request is called the status line and includes:
- protocol: HTTP/1.1
- status code: 200 
- status message: OK

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

If we send a form using GET method:

```php
<form method="GET" action="/test">
     Name: <input type="text" name="name" />
     Surame: <input type="text" name="surname" />
     <input type="submit" name="action" value="Send" />
</form>
```

Each form input will be added into the query string, so the request will look like this:

```php
GET /test?name=John&surname=Doe&action=Send HTTP/1.1
Host: example.com
```

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

### HEAD
HEAD is used to retrieve header information. Basically is identical to GET, except the server does not return the content in the HTTP response. When you send a HEAD request, it means that you are only interested in the response code and the HTTP headers, not the document itself.

### POST
In POST requests data is sent in content/body of a HTTP request (not in the URL like in GET) and Content-Type header determines type of sent data. 
Example od the post request was mentioned here: https://github.com/kdlug/php/blob/master/README.md#post-request
Content of this request looks like the following
```
RAW BODY
{"name":"John","surname":"Doe"}
```
```php
If we send a form using POST method:
<form method="POST" action="/test">
 
Name: <input type="text" name="name" />
Surname: <input type="text" name="surname" />
 
<input type="submit" name="action" value="Send" />
 
</form>
```
Each form input will be added into to the request body, so the request will look like this:

```php
POST /test?name=John&surname=Doe&action=Send HTTP/1.1
Host: example.com

name=John&surname=Doe&action=Send
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
A lot of headers occur both in requests and responses, but some of them are specific only for requests or responses. 

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
There are four types of HTTP message headers:

- General-header: These header fields have general applicability for both request and response messages.

- Client Request-header: These header fields have applicability only for request messages.

- Server Response-header: These header fields have applicability only for response messages.

- Entity-header: These header fields define meta information about the entity-body or, if no body is present, about the resource identified by the request.

In PHP there are two methods to get headers:
- `getallheaders()` gets the request headers. You can also use the $_SERVER array.  
- `headers_list()` gets the response headers.

### Most used headers

#### Host
```php
Host: example.com
```

A request header containig host name, including the domain and the subdomain.

All headers in PHP we can find in $_SERVER array. Host header can be retreived by `$_SERVER['HTTP_HOST']` or `$_SERVER['SERVER_NAME']`.

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

User-Agent header can be checked in $_SERVER['HTTP_USER_AGENT']

#### Content Type
The Content-Type entity header field indicates format (media type) of data sent in body of the requests (POST, PUT) or media type of data for GET requests.

#### Accept
Request header which can be used by the client to specify expected format of data for the response.
For instance Chrome browser can handle the following media types: 
```Accept: text/html, application/xhtml+xml, image/jxr, */*```
If response will be returned in one of above formats, client can regognize it. */* at the end means that Chrome browser supposedly
can handle every type of content.
There are a few headers which, similar to accept, are used for content negotiation: Accept-Charset, Accept-Encoding, Accept-Language
```php
Accept-Encoding: gzip,deflate
```
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
The Authorization request-header field value consists of credentials containing the authentication information of the user agent.

##### Basic
Basic authorization scheme:
- Authorization parameter is a string username:password
- String is enoded in Base64
- Encoded string (token) is sent by client in Authorization header:
```php
Authorization: Basic dGVzdDpwYXNzMTIz
```
The value decodes user:pass123.

- Token is a text an can be easily decoded, so it's not safe to send it via HTTP, use HTTPS instead. 

In PHP we can send the same Authorization header in the following way:
```php
$url = 'https://requestb.in/zx5pgqzx';

$curl = curl_init($url);

// set auth Basic and pass user and password
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
curl_setopt($curl, CURLOPT_USERPWD, 'user:pass123'); 

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($curl);
curl_close($curl);
var_dump($result);
```

To get user and password from the request we can use $_SERVER[] suberglobal array:
```php
$_SERVER['PHP_AUTH_USER']
$_SERVER['PHP_AUTH_PASSWORD']
```
##### Bearer
```php
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImp0aSI6IjNiZGRhMDYyLWI1Y2YtNDI0MC1iZGE3LTU4MTg3NmY2OGE4ZSIsImlhdCI6MTQ5NTQ4MTU5NSwiZXhwIjoxNDk1NDg1MTk1fQ.TcGlwC5Q32TT61hb_tL8H2eMl13kevHYdl7Ngb-bM5o
```
Tokens are usually associated with OAuth (Authorization Framework). JSON Web Token (JWT) is an open standard for creating access tokens.
> Read more: https://www.jsonwebtoken.io/ https://jwt.io/

### Custom Headers
Custom headers are started with the X- prefix, f.ex: X-Cache, X-Varnish in previous examples.

## Cookies
HTTP 1.1 protocol is stateless which means that connection between the client and the server is lost once the transaction ends. Server don't store any state about the client session on the server side so it doesn't really know know if 2 requests came from the same browser / client. 
There are several solutions if we want to track client sessions:
- cookies
- server side sessions
- hidden variables (forms)
- query string (URI-encoded parameters f.ex. index.php?id=some_unique_id)

Cookie is a small plain text file, stored in a client browser, containing kei=value pairs. This information can be send in next requests to the server and the server can use it to identify clients.

Flow:
1. Client sends a request without cookie (new session).
2. Server sends a response with cookie (set-cookie).
3. Client sends another requests with cookie.

Cookies are stored in HTTP headers:

#### Response cookie:
```Set-Cookie: name=value[; expires=date][; domain=domain][; path=path][; secure]```
Cookie has 4 basic options:
- cookie name, 
- domain, 
- path, 
- secure flag 
Each of the options are separated by a semicolon and space and each specifies rules about when the cookie should be sent back to the server. 
- Expires option -  indicates when the cookie should no longer be sent to the server and therefore may be deleted by the browser. The value should be in format: Wdy, DD-Mon-YYYY HH:MM:SS GMT
- Domain - indicates the domain(s) for which the cookie should be sent
- Path - indicates a URL path that must exist in the requested resource before sending the Cookie header
- Secure - is just a flag without a value specified. A secure cookie will only be sent to the server for SSL requests via HTTPS protocol. 
Example:
```
set-cookie:prov=327db351-a4cb-cf06-69b3-13ec73e49b38; domain=.stackoverflow.com; expires=Fri, 01-Jan-2055 00:00:00 GMT; path=/; HttpOnly
```

#### Request cookie
```Cookie: name1=value1; name2=value2```

Example
```
cookie:prov=327db351-a4cb-cf06-69b3-13ec73e49b38; _ga=GA1.2.1476171314.1496128198; _gid=GA1.2.1245437519.1496128198; __qca=P0-1644340840-1496128198289
```

### Curl
Cookies can be modified on the client side, so you can't trust them.

To store cookie in a file (cookiejar) we use `-c ` switcher
Example:
```
curl -c cookies.txt http://stackoverflow.com/

cat cookies.txt

# Netscape HTTP Cookie File
# http://curl.haxx.se/docs/http-cookies.html
# This file was generated by libcurl! Edit at your own risk.

#HttpOnly_.stackoverflow.com    TRUE    /       FALSE   2682374400      prov    f6d7c698-adc3-11c8-4cd1-f4fc3711369e

```
Cookie file contains:
- Domain for which cookie is intended - stackoverflow.com
- Information if a cookie is right for all machines within this domain - TRUE
- Path in a domain related to the cookie: /
- Information if the cookie should be send only via secured connection: FALSE
- Cookie expires date: 2682374400
- Name of cookie: prov
- Value: f6d7c698-adc3-11c8-4cd1-f4fc3711369e

To send cookie back to the server we use `-b` switcher. Fex.
```curl -b cookies.txt http://stackoverflow.com/```

We can also use both `-c -b` if we'd like to send and receive cookie cookie at the same time.

### PHP
To create a cookie we use setcookie() function.
```php
setcookie("visited", true);
```
Cookies from requests can be faund in superglobal variable `$_COOKIE`

To remove a cookie we can set expiration date to past. 

We can also set cookie via curl():
```php
$url = 'https://requestb.in/zx5pgqzx';

$curl = curl_init($url);

// set auth Basic and pass user and password
curl_setopt($curl, CURLOPT_COOKIE, "visited=true"); 
curl_setopt($curl, CURLOPT_USERPWD, 'user:pass123'); 

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($curl);
curl_close($curl);
var_dump($result);
```

### Session Cookie
If we don't set an expiration date - Session Cookie will be created. It's valid until the browser won't be closed.
Session cookies are not the same as cookies used to tracking user session in network applications.

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
