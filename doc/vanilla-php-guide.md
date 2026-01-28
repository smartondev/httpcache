# Vanilla PHP Guide

This guide will walk you through using the `http-cache` library in a standard PHP application.

## Installation

Install the library using Composer:

```bash
composer require smartondev/http-cache
```

## Basic Usage: Setting Cache Headers

You can use `CacheHeaderBuilder` to generate the `Cache-Control` header and other related headers for your HTTP response.

```php
<?php

require 'vendor/autoload.php';

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

// Your application logic to generate the content
$content = '<html><body><h1>Hello, World!</h1></body></html>';
$lastModifiedTimestamp = filemtime(__FILE__); // Or any other timestamp

// Create a cache header builder instance
$cacheBuilder = new CacheHeaderBuilder();

// Configure the cache headers
$cacheBuilder
    ->public()
    ->maxAge(3600) // 1 hour
    ->sharedMaxAge(7200) // 2 hours for shared caches
    ->mustRevalidate()
    ->lastModified($lastModifiedTimestamp);

// Get the headers as an array
$headers = $cacheBuilder->getHeaders();

// Send the headers
foreach ($headers as $name => $value) {
    header("$name: $value");
}

// Send the content
echo $content;
```

## Conditional Requests with ETag

You can use `ETagHeaderBuilder` and `ETagMatcher` to handle conditional requests based on the `ETag` header. This allows the server to respond with a `304 Not Modified` status if the client's cached version is still fresh.

### 1. Generating the ETag and Sending the Response

First, generate an ETag for your content and send it with the response.

```php
<?php

require 'vendor/autoload.php';

use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;

$content = '<html><body><h1>Hello, ETag!</h1></body></html>';

// Generate an ETag from the content
$etagBuilder = new ETagHeaderBuilder();
$etagBuilder->computedETag($content, 'md5'); // You can use any hashing algorithm

// Get the ETag header
$etagHeader = $etagBuilder->getHeaders();

// Send the ETag header along with other headers
header('ETag: ' . $etagHeader['etag']);
// ... send other headers and content
echo $content;
```

### 2. Matching the ETag on Subsequent Requests

On subsequent requests from the client, the browser will send an `If-None-Match` header. You can use `ETagMatcher` to check if it matches the current ETag of the resource.

```php
<?php

require 'vendor/autoload.php';

use SmartonDev\HttpCache\Matchers\ETagMatcher;
use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;

$content = '<html><body><h1>Hello, ETag!</h1></body></html>';

// Generate the current ETag for the resource
$currentETag = md5($content);

// Get the request headers
$requestHeaders = getallheaders();

// Create an ETag matcher with the request headers
$matcher = new ETagMatcher($requestHeaders);

// Check if the client's ETag matches the current ETag
$result = $matcher->matches($currentETag);

if ($result->matchesIfNoneMatchHeader()) {
    // The client's version is up-to-date
    http_response_code(304);
    exit;
}

// If it doesn't match, send the full response with the new ETag
$etagBuilder = new ETagHeaderBuilder();
$etagBuilder->etag($currentETag);
$headers = $etagBuilder->getHeaders();

foreach ($headers as $name => $value) {
    header("$name: $value");
}

echo $content;
```

## Conditional Requests with Last-Modified Date

Similarly, you can use `ModifiedMatcher` to handle conditional requests based on the `Last-Modified` header.

### 1. Sending the Last-Modified Header

Send the `Last-Modified` header with your response.

```php
<?php

require 'vendor/autoload.php';

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

$content = '<html><body><h1>Hello, Last-Modified!</h1></body></html>';
$lastModifiedTimestamp = filemtime(__FILE__);

$cacheBuilder = new CacheHeaderBuilder();
$cacheBuilder->lastModified($lastModifiedTimestamp);

$headers = $cacheBuilder->getHeaders();

foreach ($headers as $name => $value) {
    header("$name: $value");
}

echo $content;
```

### 2. Matching the Last-Modified Date on Subsequent Requests

On subsequent requests, the browser will send an `If-Modified-Since` header. Use `ModifiedMatcher` to check it.

```php
<?php

require 'vendor/autoload.php';

use SmartonDev\HttpCache\Matchers\ModifiedMatcher;
use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

$lastModifiedTimestamp = filemtime(__FILE__);

// Get request headers
$requestHeaders = getallheaders();

$matcher = new ModifiedMatcher($requestHeaders);

try {
    if ($matcher->matches($lastModifiedTimestamp)->matchesIfModifiedSinceHeader()) {
        // The client's version is up-to-date
        http_response_code(304);
        exit;
    }
} catch (\SmartonDev\HttpCache\Exceptions\DateMalformedStringException $e) {
    // Handle malformed date string in header if necessary
    // For simplicity, we'll just send the full response
}

// Send the full response
$content = '<html><body><h1>Hello, Last-Modified!</h1></body></html>';

$cacheBuilder = new CacheHeaderBuilder();
$cacheBuilder->lastModified($lastModifiedTimestamp);

$headers = $cacheBuilder->getHeaders();

foreach ($headers as $name => $value) {
    header("$name: $value");
}

echo $content;

```

*This documentation was AI-generated.*