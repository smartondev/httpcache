# Build HTTP cache headers, ETag and modified matchers

![GitHub Release](https://img.shields.io/github/v/release/smartondev/httpcache?include_prereleases)
[![GitHub License](https://img.shields.io/github/license/smartondev/httpcache)](LICENSE)
![PHPUnit - GitHub Actions](https://img.shields.io/github/actions/workflow/status/smartondev/httpcache/phpunit.yml?label=tests)
![PHPStan level 10 - GitHub Actions](https://img.shields.io/github/actions/workflow/status/smartondev/httpcache/phpstan.yml?label=PHPStan%20level%2010)
[![Coverage Status](https://img.shields.io/coverallsCoverage/github/smartondev/httpcache?label=coveralls)](https://coveralls.io/github/smartondev/httpcache?branch=main)
[![Codecov](https://img.shields.io/codecov/c/github/smartondev/httpcache?label=codecov)](https://app.codecov.io/gh/smartondev/httpcache)

This package helps you to build HTTP cache headers like `Cache-Control`, `ETag` and matchers like `If-None-Match`,
`If-Modified-Since`. It is useful for building HTTP cache headers and matchers in your application.

## Installation

```bash
composer require smartondev/httpcache
```

## Usage

### Cache headers

```php
use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

// max-age 1 hour, private, no-store
$headers = (new CacheHeaderBuilder())
    ->maxAge(hours: 1)
    ->private()
    ->noStore()
    ->toHeaders();

// max-age 60 sec, shared max age 120 sec, stale-while-revalidate 30 sec
$headers = (new CacheHeaderBuilder())
    ->maxAge(60)
    ->sharedMaxAge(120)
    ->staleWhileRevalidate(30)
    ->toHeaders();
```

#### No-cache

```php
$noCacheHeaders = (new CacheHeaderBuilder())
    ->noCache()
    ->toHeaders();
```

#### Durations

```php
$builder = (new CacheHeaderBuilder())
    ->maxAge(30) // 30 sec
    ->maxAge(seconds: 30) // 30 sec
    ->maxAge(minutes: 30) // 30 min
    ->maxAge(hours: 30) // 30 hours
    ->maxAge(days: 30) // 30 days
    ->maxAge(weeks: 30) // 30 weeks
    ->maxAge(months: 30) // 30 months
    ->maxAge(years: 30) // 30 years
    ->maxAge(days: 10, hours: 5, minutes: 30) // 10 days 5 hours 30 minutes
```

### Etag check

```php
use SmartonDev\HttpCache\Matchers\ETagMatcher;

// ETag check
$etagMatcher = (new ETagMatcher())
    ->headers($requestHeaders);
$activeEtag = '1234';
if($etagMatcher->matches($activeEtag)->matches()) {
    // 304 Not Modified
    return response(null, 304);
}
```

### Modified check

```php
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

// modified since
$modifiedMatcher = (new ModifiedMatcher())
    ->headers($requestHeaders);
if($modifiedMatcher->matches($lastModified)->matchesModifiedAt()) {
    // 304 Not Modified
    return response(null, 304);
}
```

## Mutable and immutable accessors

- `with` prefixed methods are immutable, eg. `withMaxAge()`. Methods without `with` prefix are mutable, eg. `maxAge()`.
- `without` prefixed methods are immutable, eg. `withoutMaxAge()`. Methods with `reset` prefix are mutable, eg.
  `resetMaxAge()`.

```php
$builderA = new CacheHeaderBuilder();
// mutable
$builderA->maxAge(30)
         ->resetMaxAge();

// immutable
$builderB = $builderA->withMaxAge(60);
$builderC = $builderB->withoutMaxAge();
```

## More documentation

- [CacheHeaderBuilder](doc/cache-header-builder.md): building cache headers like `Cache-Control`
- [ETagHeaderBuilder](doc/etag-header-builder.md): building ETag header
- [ETagMatcher](doc/etag-matcher.md): matching ETag headers like `If-Match`, `If-None-Match`
- [ModifiedMatcher](doc/modified-matcher.md): matching modified headers like `If-Modified-Since`, `If-Unmodified-Since`

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

This project is open-sourced software [licensed under](LICENSE).

## Author

- [Márton Somogyi](https://github.com/kamarton)
