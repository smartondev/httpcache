# Using HttpCache with Symfony

This guide provides practical examples of how to integrate the `httpcache` library into your Symfony applications to manage HTTP caching effectively.

## Installation

First, install the library using Composer:

```bash
composer require smartondev/httpcache
```

## Basic Cache Control in a Controller

You can use `CacheHeaderBuilder` directly in your controller actions to set `Cache-Control` and other related headers.

### Example: Public and Private Caching

```php
namespace App\Controller;

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'article_list')]
    public function list(): JsonResponse
    {
        // Publicly cache the list of articles for 10 minutes
        $headers = (new CacheHeaderBuilder())
            ->public()
            ->maxAge(minutes: 10)
            ->toHeaders();

        return new JsonResponse(['articles' => ...], 200, $headers);
    }

    #[Route('/account/profile', name: 'user_profile')]
    public function profile(): JsonResponse
    {
        // Privately cache the user's profile for 5 minutes
        $headers = (new CacheHeaderBuilder())
            ->private()
            ->maxAge(minutes: 5)
            ->toHeaders();

        return new JsonResponse(['user' => ...], 200, $headers);
    }
}
```

## Cache Validation with ETag (`If-None-Match`)

Cache validation helps you save bandwidth by sending a `304 Not Modified` response when the client's cached version is still fresh.

### Example: Returning a 304 Response

In this example, we generate an ETag from the resource's last update timestamp. The `ETagMatcher` checks if this ETag matches the `If-None-Match` header from the request.

```php
namespace App\Controller;

use App\Entity\Product;
use SmartonDev\HttpCache\Matchers\ETagMatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products/{id}', name: 'product_show')]
    public function show(Request $request, Product $product): Response
    {
        // Generate an ETag. A weak ETag is often sufficient.
        $etag = 'W/"' . md5($product->getUpdatedAt()->getTimestamp()) . '"';

        // Check if the client's ETag matches the current one
        $matcher = (new ETagMatcher())->headers($request->headers->all());
        if ($matcher->matches($etag)->matchesIfNoneMatchHeader()) {
            // The client's version is up-to-date, send 304
            return new Response(null, Response::HTTP_NOT_MODIFIED);
        }

        // The client's version is stale, send the full response with the ETag
        $response = $this->json($product);
        $response->headers->set('ETag', $etag);

        return $response;
    }
}
```

## Cache Validation with `Last-Modified`

This works similarly to ETags but uses timestamps.

### Example: Using `If-Modified-Since`

```php
namespace App\Controller;

use App\Entity\Document;
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    #[Route('/documents/{id}', name: 'document_show')]
    public function show(Request $request, Document $document): Response
    {
        $lastModified = $document->getUpdatedAt();

        // Check if the resource has been modified since the client's last request
        $matcher = (new ModifiedMatcher())->headers($request->headers->all());
        if ($matcher->matches($lastModified)->notModified()) {
            return new Response(null, Response::HTTP_NOT_MODIFIED);
        }

        // Send the full response with the Last-Modified header
        $response = $this->json($document);
        $response->setLastModified($lastModified);

        return $response;
    }
}
```

## Optimistic Locking with `If-Match`

Use the `If-Match` header to prevent lost updates in concurrent environments. The server will only perform the update if the client's version (ETag) matches the current version on the server.

### Example: Conditional `PUT` Request

```php
namespace App\Controller;

use App\Entity\Resource;
use Doctrine\ORM\EntityManagerInterface;
use SmartonDev\HttpCache\Matchers\ETagMatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController
{
    #[Route('/resources/{id}', name: 'resource_update', methods: ['PUT'])]
    public function update(Request $request, Resource $resource, EntityManagerInterface $em): Response
    {
        $currentEtag = 'W/"' . $resource->getVersion() . '"';

        // The If-Match header is required for this endpoint
        if (!$request->headers->has('if-match')) {
            return new Response('If-Match header is required.', Response::HTTP_PRECONDITION_REQUIRED);
        }

        // Check if the client's ETag matches the server's ETag
        $matcher = (new ETagMatcher())->headers($request->headers->all());
        if ($matcher->matches($currentEtag)->notMatchesIfMatchHeader()) {
            // The resource has been modified by someone else
            return new Response('Resource has been modified since last fetch.', Response::HTTP_PRECONDITION_FAILED);
        }

        // ETag matches, proceed with the update
        $data = json_decode($request->getContent(), true);
        $resource->updateFrom($data);
        $em->flush();

        // Return the updated resource with a new ETag
        $newEtag = 'W/"' . $resource->getVersion() . '"';
        $response = $this->json($resource);
        $response->headers->set('ETag', $newEtag);

        return $response;
    }
}
```

## Combining Headers

You can combine multiple headers for more complex caching strategies.

### Example: Public Cache with ETag and Last-Modified

```php
// ... inside a controller action
$lastModified = $resource->getUpdatedAt();
$etag = 'W/"' . md5($lastModified->getTimestamp()) . '"';

// First, check for a 304 response
$eTagMatcher = (new ETagMatcher())->headers($request->headers->all());
if ($eTagMatcher->matches($etag)->matchesIfNoneMatchHeader()) {
    return new Response(null, Response::HTTP_NOT_MODIFIED);
}

$modifiedMatcher = (new ModifiedMatcher())->headers($request->headers->all());
if ($modifiedMatcher->matches($lastModified)->notModified()) {
    return new Response(null, Response::HTTP_NOT_MODIFIED);
}

// If not modified, build cache headers for the full response
$cacheHeaders = (new CacheHeaderBuilder())
    ->public()
    ->maxAge(hours: 1)
    ->etag($etag)
    ->lastModified($lastModified)
    ->toHeaders();

return $this->json($resource, 200, $cacheHeaders);
```

By integrating these patterns, you can build a robust and efficient caching layer in your Symfony application, improving performance and reducing server load.

*This documentation was AI-generated.*