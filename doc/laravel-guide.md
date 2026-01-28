# Using HttpCache with Laravel

This guide provides practical examples of how to integrate the `httpcache` library into your Laravel applications to manage HTTP caching effectively.

## Installation

First, install the library using Composer:

```bash
composer require smartondev/httpcache
```

## Basic Cache Control in a Controller

You can use `CacheHeaderBuilder` directly in your controller actions to set `Cache-Control` and other related headers.

### Example: Public and Private Caching

```php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

class ArticleController extends Controller
{
    public function list(): JsonResponse
    {
        // Publicly cache the list of articles for 10 minutes
        $headers = (new CacheHeaderBuilder())
            ->public()
            ->maxAge(minutes: 10)
            ->toHeaders();

        return response()->json(['articles' => ...], 200, $headers);
    }

    public function profile(): JsonResponse
    {
        // Privately cache the user's profile for 5 minutes
        $headers = (new CacheHeaderBuilder())
            ->private()
            ->maxAge(minutes: 5)
            ->toHeaders();

        return response()->json(['user' => ...], 200, $headers);
    }
}
```

## Cache Validation with ETag (`If-None-Match`)

Cache validation helps you save bandwidth by sending a `304 Not Modified` response when the client's cached version is still fresh.

### Example: Returning a 304 Response

In this example, we generate an ETag from the resource's last update timestamp. The `ETagMatcher` checks if this ETag matches the `If-None-Match` header from the request.

```php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use SmartonDev\HttpCache\Matchers\ETagMatcher;

class ProductController extends Controller
{
    public function show(Request $request, Product $product): Response
    {
        // Generate an ETag. A weak ETag is often sufficient.
        $etag = 'W/"' . md5($product->updated_at->timestamp) . '"';

        // Check if the client's ETag matches the current one
        $matcher = (new ETagMatcher())->headers($request->headers->all());
        if ($matcher->matches($etag)->matchesIfNoneMatchHeader()) {
            // The client's version is up-to-date, send 304
            return response(null, 304);
        }

        // The client's version is stale, send the full response with the ETag
        return response()
            ->json($product)
            ->header('ETag', $etag);
    }
}
```

## Cache Validation with `Last-Modified`

This works similarly to ETags but uses timestamps.

### Example: Using `ModifiedMatcher`

The `ModifiedMatcher` compares the `If-Modified-Since` request header with the resource's last modification time.

```php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

class PostController extends Controller
{
    public function show(Request $request, Post $post): Response
    {
        $lastModified = $post->updated_at;

        // Check if the resource has been modified since the client's last request
        $matcher = (new ModifiedMatcher())->headers($request->headers->all());
        if ($matcher->matches($lastModified)->matchesIfModifiedSinceHeader()) {
            // The client's version is up-to-date, send 304
            return response(null, 304);
        }

        // Send the full response with the Last-Modified header
        return response()
            ->json($post)
            ->header('Last-Modified', $lastModified->toRfc7231String());
    }
}
```

## Combining Cache-Control and Validation

For a robust caching strategy, combine `Cache-Control` with validation headers (`ETag` or `Last-Modified`). This allows browsers to cache the resource and re-validate it efficiently once it becomes stale.

```php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;
use SmartonDev\HttpCache\Matchers\ETagMatcher;

class ProductController extends Controller
{
    public function show(Request $request, Product $product): Response
    {
        $etag = 'W/"' . md5($product->updated_at->timestamp) . '"';

        // Check for a matching ETag
        $matcher = (new ETagMatcher())->headers($request->headers->all());
        if ($matcher->matches($etag)->matchesIfNoneMatchHeader()) {
            return response(null, 304);
        }

        // Set cache-control headers for public caching (e.g., 10 minutes)
        $cacheHeaders = (new CacheHeaderBuilder())
            ->public()
            ->maxAge(minutes: 10)
            ->toHeaders();

        // Send the full response with ETag and Cache-Control headers
        return response()
            ->json($product)
            ->header('ETag', $etag)
            ->withHeaders($cacheHeaders);
    }
}
```

*This documentation was AI-generated.*