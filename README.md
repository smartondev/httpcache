# HTTP Cache - `smartondev/httpcache`

![GitHub Release](https://img.shields.io/github/v/release/smartondev/httpcache?include_prereleases)
[![GitHub License](https://img.shields.io/github/license/smartondev/httpcache)](LICENSE)
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/smartondev/httpcache/phpunit.yml?label=tests)
[![Coverage Status](https://img.shields.io/coverallsCoverage/github/smartondev/httpcache)](https://coveralls.io/github/smartondev/httpcache?branch=main)
[![Codecov](https://img.shields.io/codecov/c/github/smartondev/httpcache)](https://app.codecov.io/gh/smartondev/httpcache)

**This code is under development and not ready for production use.**

## Installation

```bash
composer require smartondev/httpcache
```

## Usage

```php
use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;
use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;
use SmartonDev\HttpCache\Matchers\ETagMatcher;
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

// max-age 30 day, private, no-store
$headers = (new CacheHeaderBuilder())
    ->maxAge(hours: 30)
    ->private()
    ->noStore()
    ->toHeaders();

// max-age 60 sec, shared max age 120 sec, stale-while-revalidate 30 sec
$headers = (new CacheHeaderBuilder())
    ->maxAge(60)
    ->sharedMaxAge(120)
    ->staleWhileRevalidate(30)
    ->toHeaders();

// etag
$etagMatcher = (new ETagMatcher())
    ->headers($requestHeaders);
$etagHeaderBuilder = (new ETagHeaderBuilder())
    ->computedEtag()
if($etagMatcher->matches($etag)->matches()) {
    // 304 Not Modified
    return response(null, 304);
}

// modified since
$modifiedMatcher = (new ModifiedMatcher())
    ->headers($requestHeaders);
if($modifiedMatcher->matches($lastModified)->matchesModifiedAt()) {
    // 304 Not Modified
    return response(null, 304);
}
```

## No-cache

```php
$noCacheHeaders = (new CacheHeaderBuilder())
    ->noCache()
    ->toHeaders();
```

### Durations

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

### Mutable and immutable accessors

- `with` prefixed methods are immutable, eg. `withMaxAge()`. Methods without `with` prefix are mutable, eg. `maxAge()`.
- `without` prefixed methods are immutable, eg. `withoutMaxAge()`. Methods with `reset` prefix are mutable, eg.
  `resetMaxAge()`.

```php
$builderA = new CacheHeaderBuilder();
// mutable
$builderA->maxAge(30);
$builderA->resetMaxAge();

// immutable
$builderB = $builderA->withMaxAge(60);
$builderC = $builderB->withoutMaxAge();
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

This project is open-sourced software [licensed under](LICENSE).

## Author

- [Márton Somogyi](https://github.com/kamarton)
