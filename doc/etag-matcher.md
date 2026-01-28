# `ETagMatcher` class

The `ETagMatcher` class is used to match ETags against conditional HTTP headers like `If-Match` and `If-None-Match`. These headers are commonly used for optimistic concurrency control and efficient cache validation.

## Basic Usage

### If-None-Match (Cache Validation)

The `If-None-Match` header is primarily used for cache validation. If the current ETag matches the one in the header, the resource hasn't changed, and you can return a `304 Not Modified` response.

```php
use SmartonDev\HttpCache\Matchers\ETagMatcher;

// Get request headers (e.g., from $_SERVER or your framework)
$requestHeaders = [
    'if-none-match' => '"v1.2.3"'
];

// Current ETag of the resource
$currentEtag = '"v1.2.3"';

$matcher = (new ETagMatcher())->headers($requestHeaders);

if ($matcher->matches($currentEtag)->matchesIfNoneMatchHeader()) {
    // ETag matches - resource hasn't changed
    // Return 304 Not Modified
    return response(null, 304);
}

// ETag doesn't match - return full content with new ETag
return response($content, 200, ['etag' => $currentEtag]);
```

### If-Match (Conditional Updates)

The `If-Match` header is used for conditional updates to prevent conflicts. The request only proceeds if the ETag matches the current resource version.

```php
use SmartonDev\HttpCache\Matchers\ETagMatcher;

// Get request headers from PUT/PATCH/DELETE request
$requestHeaders = [
    'if-match' => '"v1.2.3"'
];

// Current ETag of the resource
$currentEtag = '"v1.2.3"';

$matcher = (new ETagMatcher())->headers($requestHeaders);

if ($matcher->matches($currentEtag)->notMatchesIfMatchHeader()) {
    // ETag doesn't match - resource was modified by someone else
    // Return 412 Precondition Failed
    return response(['error' => 'Resource was modified'], 412);
}

// ETag matches - safe to update
$resource->update($data);
return response($resource, 200);
```

## Available Methods

### Setting Headers

#### `headers(array $headers): static`

Set all HTTP headers at once (mutable).

```php
$matcher = (new ETagMatcher())
    ->headers([
        'if-match' => '"abc123"',
        'if-none-match' => '"xyz789"'
    ]);
```

#### `withHeaders(array $headers): static`

Set all HTTP headers at once (immutable - returns new instance).

```php
$matcher = (new ETagMatcher())
    ->withHeaders(['if-match' => '"abc123"']);
```

### If-Match Header

#### `ifMatchHeaderValue(string|array $ifMatch): static`

Set the `If-Match` header value (mutable).

```php
// Single value
$matcher->ifMatchHeaderValue('"v1.0.0"');

// Multiple values
$matcher->ifMatchHeaderValue(['"v1.0.0"', '"v1.0.1"']);
```

#### `withIfMatchHeaderValue(string|array $ifMatch): static`

Set the `If-Match` header value (immutable - returns new instance).

```php
$newMatcher = $matcher->withIfMatchHeaderValue('"v2.0.0"');
```

#### `getIfMatchHeader(): ?string`

Get the `If-Match` header value.

```php
$ifMatch = $matcher->getIfMatchHeader();
// Returns: '"v1.0.0"' or null
```

#### `hasIfMatchHeader(): bool`

Check if the `If-Match` header is present.

```php
if ($matcher->hasIfMatchHeader()) {
    // If-Match header is present
}
```

### If-None-Match Header

#### `ifNoneMatchHeaderValue(string|array $ifNoneMatch): static`

Set the `If-None-Match` header value (mutable).

```php
// Single value
$matcher->ifNoneMatchHeaderValue('"v1.0.0"');

// Multiple values
$matcher->ifNoneMatchHeaderValue(['"v1.0.0"', '"v1.0.1"']);
```

#### `withIfNoneMatchHeaderValue(string|array $ifNoneMatch): static`

Set the `If-None-Match` header value (immutable - returns new instance).

```php
$newMatcher = $matcher->withIfNoneMatchHeaderValue('"v2.0.0"');
```

#### `getIfNoneMatchHeader(): ?string`

Get the `If-None-Match` header value.

```php
$ifNoneMatch = $matcher->getIfNoneMatchHeader();
// Returns: '"v1.0.0"' or null
```

#### `hasIfNoneMatchHeader(): bool`

Check if the `If-None-Match` header is present.

```php
if ($matcher->hasIfNoneMatchHeader()) {
    // If-None-Match header is present
}
```

### Matching

#### `matches(null|string|array $etag): ETagMatcherResult`

Match the provided ETag(s) against the headers and return a result object.

```php
// Single ETag
$result = $matcher->matches('"v1.0.0"');

// Multiple ETags (any can match)
$result = $matcher->matches(['"v1.0.0"', '"v1.0.1"']);

// No ETag
$result = $matcher->matches(null);
```

## ETagMatcherResult Methods

The `matches()` method returns an `ETagMatcherResult` object with the following methods:

### `matchesIfMatchHeader(): bool`

Returns `true` if the ETag matches the `If-Match` header.

```php
if ($result->matchesIfMatchHeader()) {
    // Safe to proceed with update
}
```

### `notMatchesIfMatchHeader(): bool`

Returns `true` if the ETag does NOT match the `If-Match` header.

```php
if ($result->notMatchesIfMatchHeader()) {
    // Return 412 Precondition Failed
}
```

### `matchesIfNoneMatchHeader(): bool`

Returns `true` if the ETag matches the `If-None-Match` header.

```php
if ($result->matchesIfNoneMatchHeader()) {
    // Return 304 Not Modified
}
```

### `notMatchesIfNoneMatchHeader(): bool`

Returns `true` if the ETag does NOT match the `If-None-Match` header.

```php
if ($result->notMatchesIfNoneMatchHeader()) {
    // Return full content
}
```

## Practical Examples

### Laravel Cache Validation

```php
use SmartonDev\HttpCache\Matchers\ETagMatcher;

class ProductController extends Controller
{
    public function show(Request $request, int $id)
    {
        $product = Product::findOrFail($id);
        $etag = '"' . md5($product->updated_at->timestamp) . '"';
        
        $matcher = (new ETagMatcher())->headers($request->headers->all());
        
        if ($matcher->matches($etag)->matchesIfNoneMatchHeader()) {
            return response()->noContent(304);
        }
        
        return response()->json($product)->header('ETag', $etag);
    }
}
```

### Symfony Optimistic Locking

```php
use SmartonDev\HttpCache\Matchers\ETagMatcher;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends AbstractController
{
    #[Route('/documents/{id}', methods: ['PUT'])]
    public function update(Request $request, int $id): Response
    {
        $document = $this->documentRepository->find($id);
        $currentEtag = '"' . $document->getVersion() . '"';
        
        $matcher = (new ETagMatcher())->headers($request->headers->all());
        
        if ($matcher->hasIfMatchHeader() && 
            $matcher->matches($currentEtag)->notMatchesIfMatchHeader()) {
            return $this->json(
                ['error' => 'Document was modified by another user'],
                Response::HTTP_PRECONDITION_FAILED
            );
        }
        
        // Proceed with update
        $document->update($request->getContent());
        $newEtag = '"' . $document->getVersion() . '"';
        
        return $this->json($document, Response::HTTP_OK, ['ETag' => $newEtag]);
    }
}
```

### REST API with Multiple ETags

```php
use SmartonDev\HttpCache\Matchers\ETagMatcher;

// Client might have multiple cached versions
$requestHeaders = [
    'if-none-match' => '"v1.0.0", "v1.0.1", "v1.1.0"'
];

$currentVersion = '"v1.0.1"';

$matcher = (new ETagMatcher())->headers($requestHeaders);

// Check if any of the client's cached versions match
if ($matcher->matches([$currentVersion])->matchesIfNoneMatchHeader()) {
    // One of the client's versions is still current
    http_response_code(304);
    exit;
}

// None match - send full response
header('ETag: ' . $currentVersion);
echo json_encode($data);
```

### Combining with Cache Headers

```php
use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;
use SmartonDev\HttpCache\Matchers\ETagMatcher;

function sendCachedResponse($data, $lastModified, $requestHeaders)
{
    $etag = '"' . md5(serialize($data) . $lastModified) . '"';
    
    // Check if client has current version
    $matcher = (new ETagMatcher())->headers($requestHeaders);
    if ($matcher->matches($etag)->matchesIfNoneMatchHeader()) {
        http_response_code(304);
        exit;
    }
    
    // Build cache headers for new response
    $cacheHeaders = (new CacheHeaderBuilder())
        ->maxAge(hours: 1)
        ->public()
        ->etag($etag)
        ->lastModified($lastModified)
        ->toHeaders();
    
    foreach ($cacheHeaders as $name => $value) {
        header("$name: $value");
    }
    
    echo json_encode($data);
}
```

## HTTP Status Codes

When working with ETags, use these HTTP status codes:

- **200 OK**: ETag doesn't match, return full content
- **304 Not Modified**: `If-None-Match` matches current ETag
- **412 Precondition Failed**: `If-Match` doesn't match current ETag (for updates)
- **428 Precondition Required**: Client must provide `If-Match` header (optional enforcement)

## Best Practices

1. **Always quote ETags**: ETags should be wrapped in double quotes, e.g., `"v1.0.0"`
2. **Use weak ETags for semantically equivalent content**: Prefix with `W/`, e.g., `W/"v1.0.0"`
3. **Combine with Cache-Control**: ETags work best with appropriate cache headers
4. **Generate stable ETags**: Use content hash or version number for consistency
5. **Support multiple ETags**: Clients can send multiple ETags in `If-None-Match`
6. **Require If-Match for updates**: Prevent lost updates in concurrent scenarios

## Weak vs Strong ETags

```php
// Strong ETag - content is byte-for-byte identical
$strongEtag = '"v1.0.0"';

// Weak ETag - content is semantically equivalent but may differ slightly
$weakEtag = 'W/"v1.0.0"';

$matcher = (new ETagMatcher())
    ->headers(['if-none-match' => $weakEtag]);

// The matcher compares exact string values
if ($matcher->matches($weakEtag)->matchesIfNoneMatchHeader()) {
    // Matches
}
```

## Related

- [ETag Header Builder](./etag-header-builder.md)

---

*This documentation was AI-generated.*
