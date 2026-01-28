# `ModifiedMatcher` class

The `ModifiedMatcher` class is used to check if the request headers contain the `If-Modified-Since` header and if the
last modified date is greater than the date in the header.

Additionally, it can check if the request headers contain the `If-Unmodified-Since` header and if the last modified date
is less than or equal to the date in the header.

## Basic Usage

### If-Modified-Since (Cache Validation)

The `If-Modified-Since` header is primarily used for cache validation. If the resource hasn't been modified since the date in the header, you can return a `304 Not Modified` response.

```php
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

// Get request headers (e.g., from $_SERVER or your framework)
$requestHeaders = [
    'if-modified-since' => 'Tue, 15 Nov 1994 12:45:26 GMT'
];

// Last modified time of the resource
$lastModified = new \DateTime('2024-01-28 10:30:00');

$matcher = (new ModifiedMatcher())->headers($requestHeaders);

$result = $matcher->matches($lastModified);

if (!$result->isModifiedSince()) {
    // Resource hasn't been modified - return 304 Not Modified
    return response(null, 304);
}

// Resource was modified - return full content with Last-Modified header
return response($content, 200, [
    'last-modified' => $lastModified->format('D, d M Y H:i:s \G\M\T')
]);
```

### If-Unmodified-Since (Conditional Updates)

The `If-Unmodified-Since` header is used for conditional updates to prevent conflicts. The request only proceeds if the resource hasn't been modified since the specified date.

```php
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

// Get request headers from PUT/PATCH/DELETE request
$requestHeaders = [
    'if-unmodified-since' => 'Tue, 15 Nov 1994 12:45:26 GMT'
];

// Last modified time of the resource
$lastModified = new \DateTime('2024-01-28 10:30:00');

$matcher = (new ModifiedMatcher())->headers($requestHeaders);

$result = $matcher->matches($lastModified);

if (!$result->isUnmodifiedSince()) {
    // Resource was modified by someone else
    // Return 412 Precondition Failed
    return response(['error' => 'Resource was modified'], 412);
}

// Resource hasn't been modified - safe to update
$resource->update($data);
return response($resource, 200);
```

## Available Methods

### Setting Headers

#### `headers(array $headers): static`

Set all HTTP headers at once (mutable).

```php
$matcher = (new ModifiedMatcher())
    ->headers([
        'if-modified-since' => 'Tue, 15 Nov 1994 12:45:26 GMT',
        'if-unmodified-since' => 'Wed, 16 Nov 1994 12:45:26 GMT'
    ]);
```

#### `withHeaders(array $headers): static`

Set all HTTP headers at once (immutable - returns new instance).

```php
$matcher = (new ModifiedMatcher())
    ->withHeaders(['if-modified-since' => 'Tue, 15 Nov 1994 12:45:26 GMT']);
```

### If-Modified-Since Header

#### `ifModifiedSinceHeader(string|array $ifModifiedSinceHeaderValue): static`

Set the `If-Modified-Since` header value (mutable).

```php
// Single value
$matcher->ifModifiedSinceHeader('Tue, 15 Nov 1994 12:45:26 GMT');

// Multiple values (first will be used)
$matcher->ifModifiedSinceHeader(['Tue, 15 Nov 1994 12:45:26 GMT', 'Wed, 16 Nov 1994 12:45:26 GMT']);
```

#### `withIfModifiedSinceHeader(string|array $ifModifiedSinceHeaderValue): static`

Set the `If-Modified-Since` header value (immutable - returns new instance).

```php
$newMatcher = $matcher->withIfModifiedSinceHeader('Wed, 16 Nov 1994 12:45:26 GMT');
```

#### `getIfModifiedSinceHeader(): ?string`

Get the `If-Modified-Since` header value.

```php
$ifModifiedSince = $matcher->getIfModifiedSinceHeader();
// Returns: 'Tue, 15 Nov 1994 12:45:26 GMT' or null
```

#### `hasIfModifiedSinceHeader(): bool`

Check if the `If-Modified-Since` header is present.

```php
if ($matcher->hasIfModifiedSinceHeader()) {
    // If-Modified-Since header is present
}
```

#### `isValidIfModifiedSinceHeader(): bool`

Check if the `If-Modified-Since` header value is a valid date string.

```php
if ($matcher->isValidIfModifiedSinceHeader()) {
    // Header contains a valid HTTP date string
}
```

#### `isInvalidIfModifiedSinceHeader(): bool`

Check if the `If-Modified-Since` header value is invalid or missing.

```php
if ($matcher->isInvalidIfModifiedSinceHeader()) {
    // Header is missing or contains an invalid date string
}
```

#### `getIfModifiedSinceHeaderAsTimestamp(): ?int`

Get the `If-Modified-Since` header value as a Unix timestamp.

```php
$timestamp = $matcher->getIfModifiedSinceHeaderAsTimestamp();
// Returns: 784903526 or null

// Throws DateMalformedStringException if header value is invalid
```

### If-Unmodified-Since Header

#### `ifUnmodifiedSinceHeader(string|array $ifUnmodifiedSinceHeaderValue): static`

Set the `If-Unmodified-Since` header value (mutable).

```php
// Single value
$matcher->ifUnmodifiedSinceHeader('Tue, 15 Nov 1994 12:45:26 GMT');

// Multiple values (first will be used)
$matcher->ifUnmodifiedSinceHeader(['Tue, 15 Nov 1994 12:45:26 GMT', 'Wed, 16 Nov 1994 12:45:26 GMT']);
```

#### `withIfUnmodifiedSinceHeader(string|array $ifUnmodifiedSinceHeaderValue): static`

Set the `If-Unmodified-Since` header value (immutable - returns new instance).

```php
$newMatcher = $matcher->withIfUnmodifiedSinceHeader('Wed, 16 Nov 1994 12:45:26 GMT');
```

#### `getIfUnmodifiedSinceHeader(): ?string`

Get the `If-Unmodified-Since` header value.

```php
$ifUnmodifiedSince = $matcher->getIfUnmodifiedSinceHeader();
// Returns: 'Tue, 15 Nov 1994 12:45:26 GMT' or null
```

#### `hasIfUnmodifiedSinceHeader(): bool`

Check if the `If-Unmodified-Since` header is present.

```php
if ($matcher->hasIfUnmodifiedSinceHeader()) {
    // If-Unmodified-Since header is present
}
```

#### `isValidIfUnmodifiedSinceHeader(): bool`

Check if the `If-Unmodified-Since` header value is a valid date string.

```php
if ($matcher->isValidIfUnmodifiedSinceHeader()) {
    // Header contains a valid HTTP date string
}
```

#### `isInvalidIfUnmodifiedSinceHeader(): bool`

Check if the `If-Unmodified-Since` header value is invalid or missing.

```php
if ($matcher->isInvalidIfUnmodifiedSinceHeader()) {
    // Header is missing or contains an invalid date string
}
```

#### `getIfUnmodifiedSinceHeaderAsTimestamp(): ?int`

Get the `If-Unmodified-Since` header value as a Unix timestamp.

```php
$timestamp = $matcher->getIfUnmodifiedSinceHeaderAsTimestamp();
// Returns: 784903526 or null

// Throws DateMalformedStringException if header value is invalid
```

### Matching

#### `matches(int|string|\DateTime $baseData): ModifiedMatcherResult`

Match the provided date against the headers and return a result object. The `baseData` parameter can be:
- Unix timestamp (int)
- Date string (string)
- DateTime object (\DateTime)

```php
// Using DateTime object
$lastModified = new \DateTime('2024-01-28 10:30:00');
$result = $matcher->matches($lastModified);

// Using Unix timestamp
$result = $matcher->matches(1706437800);

// Using date string
$result = $matcher->matches('2024-01-28 10:30:00');
```

## ModifiedMatcherResult Methods

The `matches()` method returns a `ModifiedMatcherResult` object with the following methods:

### `isModifiedSince(): bool`

Returns `true` if the resource has been modified since the date specified in the `If-Modified-Since` or `If-Unmodified-Since` header.

This method returns `true` when:
- The base date is **greater than** the `If-Modified-Since` header value, OR
- The base date is **greater than** the `If-Unmodified-Since` header value

```php
$result = $matcher->matches($lastModified);

if ($result->isModifiedSince()) {
    // Resource was modified - return full content
    return response($content, 200);
} else {
    // Resource hasn't been modified - return 304
    return response(null, 304);
}
```

### `matchesModifiedAt(): bool`

Returns `true` if the resource was modified exactly at the time specified in the `If-Modified-Since` header.

```php
$result = $matcher->matches($lastModified);

if ($result->matchesModifiedAt()) {
    // Resource was modified at exactly this time
}
```

### `isUnmodifiedSince(): bool`

Returns `true` if the resource has **not** been modified since the date specified in the `If-Unmodified-Since` header.

This method returns `true` when:
- The `If-Unmodified-Since` header is present, AND
- The base date is **less than or equal to** the header value

```php
$result = $matcher->matches($lastModified);

if ($result->isUnmodifiedSince()) {
    // Safe to proceed with update
    $resource->update($data);
} else {
    // Resource was modified - return 412 Precondition Failed
    return response(['error' => 'Resource was modified'], 412);
}
```

## Complete Examples

### Example 1: GET Request with Cache Validation

```php
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

// Get request headers
$requestHeaders = getallheaders(); // or from your framework

// Get resource and its last modified time
$article = Article::find($id);
$lastModified = $article->updated_at; // DateTime object

$matcher = (new ModifiedMatcher())->headers($requestHeaders);
$result = $matcher->matches($lastModified);

// Check if resource hasn't been modified
if (!$result->isModifiedSince()) {
    header('HTTP/1.1 304 Not Modified');
    exit;
}

// Resource was modified - return full content
header('Last-Modified: ' . $lastModified->format('D, d M Y H:i:s \G\M\T'));
echo json_encode($article);
```

### Example 2: PUT Request with Conditional Update

```php
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

// Get request headers
$requestHeaders = getallheaders();

// Get resource and its last modified time
$article = Article::find($id);
$lastModified = $article->updated_at;

$matcher = (new ModifiedMatcher())->headers($requestHeaders);

// Check if If-Unmodified-Since header is present
if ($matcher->hasIfUnmodifiedSinceHeader()) {
    $result = $matcher->matches($lastModified);
    
    if (!$result->isUnmodifiedSince()) {
        // Resource was modified by someone else
        header('HTTP/1.1 412 Precondition Failed');
        echo json_encode(['error' => 'Resource was modified by another user']);
        exit;
    }
}

// Safe to update
$article->update($data);
echo json_encode($article);
```

### Example 3: Combining with ETag

```php
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;
use SmartonDev\HttpCache\Matchers\ETagMatcher;

$requestHeaders = getallheaders();

$article = Article::find($id);
$lastModified = $article->updated_at;
$etag = '"' . md5($article->content) . '"';

// Check If-None-Match (ETag takes precedence)
$etagMatcher = (new ETagMatcher())->headers($requestHeaders);
if ($etagMatcher->hasIfNoneMatchHeader()) {
    $etagResult = $etagMatcher->matches($etag);
    if ($etagResult->matchesIfNoneMatchHeader()) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }
}

// Check If-Modified-Since
$modifiedMatcher = (new ModifiedMatcher())->headers($requestHeaders);
if ($modifiedMatcher->hasIfModifiedSinceHeader()) {
    $modifiedResult = $modifiedMatcher->matches($lastModified);
    if (!$modifiedResult->isModifiedSince()) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }
}

// Return full content with both headers
header('Last-Modified: ' . $lastModified->format('D, d M Y H:i:s \G\M\T'));
header('ETag: ' . $etag);
echo json_encode($article);
```

## HTTP Status Codes

When working with modification dates, these HTTP status codes are commonly used:

- **200 OK**: Resource was modified, return full content
- **304 Not Modified**: Resource hasn't been modified since the date in `If-Modified-Since`
- **412 Precondition Failed**: Resource was modified, fails the `If-Unmodified-Since` check

## Date Format

All date values must be in HTTP date format (RFC 7231):

```
Day, DD Mon YYYY HH:MM:SS GMT
```

Example: `Tue, 15 Nov 1994 12:45:26 GMT`

You can format dates using PHP's `DateTime`:

```php
$date = new \DateTime();
$httpDate = $date->format('D, d M Y H:i:s \G\M\T');
```

## Error Handling

The `getIfModifiedSinceHeaderAsTimestamp()` and `getIfUnmodifiedSinceHeaderAsTimestamp()` methods throw a `DateMalformedStringException` if the header value is not a valid date string.

```php
use SmartonDev\HttpCache\Exceptions\DateMalformedStringException;

try {
    $timestamp = $matcher->getIfModifiedSinceHeaderAsTimestamp();
} catch (DateMalformedStringException $e) {
    // Handle invalid date format
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}
```

## Related Classes

- [`ETagMatcher`](etag-matcher.md) - Match ETags for conditional requests
- [`CacheHeaderBuilder`](cache-header-builder.md) - Build Cache-Control headers
- [`ETagHeaderBuilder`](etag-header-builder.md) - Build ETag headers

*This documentation was AI-generated.*
