# Using HttpCache with Slim

This guide provides practical examples of how to integrate the `httpcache` library into your Slim applications to manage HTTP caching effectively.

## Installation

First, install the library using Composer:

```bash
composer require smartondev/httpcache
```

## Basic Cache Control in a Route

You can use `CacheHeaderBuilder` directly in your route callables to set `Cache-Control` and other related headers.

### Example: Public and Private Caching

```php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

$app = AppFactory::create();

// Publicly cache the list of articles for 10 minutes
$app->get('/articles', function (Request $request, Response $response) {
    $headers = (new CacheHeaderBuilder())
        ->public()
        ->maxAge(minutes: 10)
        ->toHeaders();

    foreach ($headers as $name => $value) {
        $response = $response->withHeader($name, $value);
    }
    
    $data = ['articles' => ...];
    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type', 'application/json');
});

// Privately cache the user's profile for 5 minutes
$app->get('/account/profile', function (Request $request, Response $response) {
    $headers = (new CacheHeaderBuilder())
        ->private()
        ->maxAge(minutes: 5)
        ->toHeaders();

    foreach ($headers as $name => $value) {
        $response = $response->withHeader($name, $value);
    }

    $data = ['user' => ...];
    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
```

## Cache Validation with ETag (`If-None-Match`)

Cache validation helps you save bandwidth by sending a `304 Not Modified` response when the client's cached version is still fresh.

### Example: Returning a 304 Response

In this example, we generate an ETag from the resource's last update timestamp. The `ETagMatcher` checks if this ETag matches the `If-None-Match` header from the request.

```php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use SmartonDev\HttpCache\Matchers\ETagMatcher;

$app = AppFactory::create();

$app->get('/products/{id}', function (Request $request, Response $response, array $args) {
    // Assume $product is fetched from a database
    $product = fetch_product($args['id']);
    
    // Generate an ETag. A weak ETag is often sufficient.
    $etag = 'W/"' . md5($product->updated_at) . '"';

    // Check if the client's ETag matches the current one
    $matcher = (new ETagMatcher())->headers($request->getHeaders());
    if ($matcher->matches($etag)->matchesIfNoneMatchHeader()) {
        // The client's version is up-to-date, send 304
        return $response->withStatus(304);
    }

    // The client's version is stale, send the full response with the ETag
    $response->getBody()->write(json_encode($product));
    
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('ETag', $etag);
});

$app->run();
```

## Cache Validation with `Last-Modified`

This works similarly to ETags but uses timestamps.

### Example: Returning a 304 Response

```php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;
use SmartonDev\HttpCache\Helpers\TimeHelper;

$app = AppFactory::create();

$app->get('/articles/{id}', function (Request $request, Response $response, array $args) {
    // Assume $article is fetched from a database
    $article = fetch_article($args['id']);
    $lastModified = new \DateTime($article->updated_at);

    // Check if the resource has been modified since the client's last request
    $matcher = (new ModifiedMatcher())->headers($request->getHeaders());
    if ($matcher->matches($lastModified)->matchesIfModifiedSinceHeader()) {
        // The client's version is up-to-date, send 304
        return $response->withStatus(304);
    }

    // Send the full response with the Last-Modified header
    $response->getBody()->write(json_encode($article));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Last-Modified', TimeHelper::toRFC1123($lastModified));
});

$app->run();
```

*This documentation was AI-generated.*