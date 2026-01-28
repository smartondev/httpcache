# `CacheHeaderBuilder` class

The `CacheHeaderBuilder` class is used to build cache headers like `Cache-Control`, `ETag`, `Last-Modified`, `Expires`, and `Age`.

## Basic Usage Examples

### No-cache

Strongly no caching. No caching in browser, no caching in proxy.

Recommended for sensitive data or data that changes frequently.

```php
$noCacheHeaders = (new CacheHeaderBuilder())
    ->noCache()
    ->toHeaders();
```

### Caching in browser in private mode

Caching in browser for 1 hour. No caching in proxy.

```php
$headers = (new CacheHeaderBuilder())
    ->maxAge(hours: 1)
    ->private()
    ->toHeaders();
```

### Caching public

Caching in browser (and in proxy) for 1 hour.

```php
$headers = (new CacheHeaderBuilder())
    ->maxAge(hours: 1)
    ->public()
    ->toHeaders();
```

### Caching in CDN

Caching in CDN for 60 seconds, and in browser for 30 seconds.

```php
$headers = (new CacheHeaderBuilder())
    ->maxAge(30)
    ->public()
    ->sharedMaxAge(60)
    ->toHeaders();
```

## Available Methods

### No Cache

Sets `Cache-Control: must-revalidate, no-store, private, no-cache` and `Pragma: no-cache`.

```php
$headers = (new CacheHeaderBuilder())
    ->noCache()
    ->toHeaders();
```

### Max Age

Specifies the maximum amount of time a resource will be considered fresh.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
// Cache for 1 hour
$builder->maxAge(hours: 1);

// Cache for 30 seconds
$builder->maxAge(30);

// Cache for 7 days
$builder->maxAge(days: 7);

// Cache for 1 year, 2 months, 3 days
$builder->maxAge(years: 1, months: 2, days: 3);
```

### Shared Max Age

Specifies the maximum amount of time a resource will be considered fresh by a shared cache (like CDN).

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
// Cache in browser for 30 seconds, in CDN for 60 seconds
$builder->maxAge(30)->public()->sharedMaxAge(60);
```

### Private

Indicates that a cache must store the response only for a single user (browser cache only).

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
$builder->maxAge(hours: 1)->private();
```

### Public

Indicates that a cache must store the response for all users (browser and proxy/CDN).

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
$builder->maxAge(hours: 1)->public();
```

### Must Revalidate

Indicates that once a resource becomes stale, a cache must not use the response without successful validation on the origin server.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
$builder->maxAge(hours: 1)->mustRevalidate();
```

### Proxy Revalidate

Similar to `must-revalidate`, but applies only to shared caches (proxies/CDNs).

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
$builder->maxAge(hours: 1)->public()->proxyRevalidate();
```

### No Store

Indicates that a cache must not store any part of the response (private or shared).

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
$builder->noStore()->private();
```

### Must Understand

Indicates that a cache must understand the request before storing it.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
$builder->maxAge(hours: 1)->mustUnderstand();
```

### No Transform

Indicates that a cache must not transform the response (e.g., no image conversion).

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
$builder->maxAge(hours: 1)->noTransform();
```

### Immutable

Indicates that the response will not be updated while it's fresh.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
$builder->maxAge(years: 1)->immutable()->public();
```

### Stale While Revalidate

Indicates that a cache can serve a stale response while revalidating it in the background.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
// Cache for 1 hour, but can serve stale content for up to 24 hours while revalidating
$builder->maxAge(hours: 1)->staleWhileRevalidate(hours: 24);
```

### Stale If Error

Indicates that a cache can serve a stale response if an error occurs while revalidating.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)

```php
// Cache for 1 hour, but can serve stale content for up to 24 hours if an error occurs
$builder->maxAge(hours: 1)->staleIfError(hours: 24);
```

### ETag

Sets the ETag header for cache validation.

```php
// Strong ETag
$builder->etag('"version-1.0"');

// Weak ETag
$builder->etag('W/"version-1.0"');

// Using ETagHeaderBuilder
$builder->etag(new ETagHeaderBuilder('version-1.0'));
```

### Last Modified

Sets the `Last-Modified` header.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Last-Modified)

```php
// Using timestamp
$builder->lastModified(time());

// Using DateTime object
$builder->lastModified(new DateTime('2024-01-01'));

// Using date string
$builder->lastModified('2024-01-01 12:00:00');
```

### Expires

Sets the `Expires` header.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires)

```php
// Using timestamp
$builder->expires(time() + 3600);

// Using DateTime object
$builder->expires(new DateTime('+1 hour'));

// Using date string
$builder->expires('2024-01-01 12:00:00');
```

### Age

Sets the `Age` header that contains the time in seconds the object has been in a proxy cache.

[MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Age)

```php
// Content has been cached for 1 hour
$builder->age(3600);
```

### Reset

Resets the builder to its default state.

```php
// All settings are cleared
$builder->maxAge(hours: 1)->reset();
```

### Utility Methods

#### Convert to Headers Array

Returns an associative array of HTTP headers.

```php
$headers = $builder->maxAge(hours: 1)->private()->toHeaders();
// Returns: ['cache-control' => 'max-age=3600, private']
```

#### Check if No-Cache is Set

```php
if ($builder->isNoCache()) {
    // Handle no-cache scenario
}
```

#### Check if Builder is Empty

```php
if ($builder->isEmpty()) {
    // No cache headers configured
}
```

#### Check if ETag is Set

```php
if ($builder->hasETag()) {
    $etag = $builder->getETag();
}
```

#### Check if Last-Modified is Set

```php
if ($builder->hasLastModified()) {
    // Last-Modified header is set
}
```

---

*This documentation was AI-generated.*

