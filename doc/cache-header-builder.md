# `CacheHeaderBuilder` class

The `CacheHeaderBuilder` class is used to build cache headers like `Cache-Control`, `ETag`.

## Usage

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