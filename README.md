# HTTP Cache - `smartondev/httpcache`

**This code is under development and not ready for production use.**

## Installation

```bash
composer require smartondev/httpcache
```

## Usage

```php
use Smartondev\HttpCache\CacheHeaderBuilder;
use Smartondev\HttpCache\ETagHeaderBuilder;
use Smartondev\HttpCache\ETagMatcher;
use SmartonDev\HttpCache\ModifiedMatcher;

// max-age 30 day, private, no-store
$headers = (new CacheHeaderBuilder())
    ->withMaxAge(hours: 30)
    ->withPrivate()
    ->withNoStore()
    ->toHeaders();

// max-age 60 sec, shared max age 120 sec, stale-while-revalidate 30 sec
$headers = (new CacheHeaderBuilder())
    ->withMaxAge(60)
    ->withSharedMaxAge(120)
    ->withStaleWhileRevalidate(30)
    ->toHeaders();

// etag
$etagMatcher = (new ETagMatcher())
    ->withHeaders($requestHeaders);
$etagHeaderBuilder = (new ETagHeaderBuilder())
    ->withComputedEtag()
if($etagMatcher->matches($etag)->matches()) {
    // 304 Not Modified
    return response(null, 304);
}

// modified since
$modifiedMatcher = (new ModifiedMatcher())
    ->withHeaders($requestHeaders);
if($modifiedMatcher->matches($lastModified)->isBeforeModifiedSince()) {
    // 304 Not Modified
    return response(null, 304);
}
```

...

### Durations

```php
$headers = (new CacheHeaderBuilder())
    ->withMaxAge(30) // 30 sec
    ->withMaxAge(seconds: 30) // 30 sec
    ->withMaxAge(minutes: 30) // 30 min
    ->withMaxAge(hours: 30) // 30 hours
    ->withMaxAge(days: 30) // 30 days
    ->withMaxAge(weeks: 30) // 30 weeks
    ->withMaxAge(months: 30) // 30 months
    ->withMaxAge(years: 30) // 30 years
    ->withMaxAge(days: 10, hours: 5, minutes: 30) // 10 days 5 hours 30 minutes
    ->toHeaders();
```

...

## Author

- [Márton Somogyi](https://github.com/kamarton)
