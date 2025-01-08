<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;
use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;

it('is empty', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->isEmpty())->toBeTrue()
        ->and($builder->withMaxAge(10)->isEmpty())->toBeFalse();
});

it('is empty multiple stage', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->isEmpty())->toBeTrue();
    $builder->noCache();
    expect($builder->isEmpty())->toBeFalse();
    $builder->reset();
    expect($builder->isEmpty())->toBeTrue();
});

it('is not empty', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->isNotEmpty())->toBeFalse()
        ->and($builder->withMaxAge(10)->isNotEmpty())->toBeTrue();
});

it('is not empty multiple stage', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->isNotEmpty())->toBeFalse();
    $builder->noCache();
    expect($builder->isNotEmpty())->toBeTrue();
    $builder->reset();
    expect($builder->isNotEmpty())->toBeFalse();
});

it('reset', function (CacheHeaderBuilder $builder) {
    expect($builder->toHeaders())->not()->toBeEmpty();
    $builder->reset();
    expect($builder->toHeaders())->toBeEmpty();
})->with([
    'no cache' => [(new CacheHeaderBuilder())->withNoCache()],
    'private' => [(new CacheHeaderBuilder())->withPrivate()],
    'public' => [(new CacheHeaderBuilder())->withPublic()],
    'no store' => [(new CacheHeaderBuilder())->withNoStore()],
    'must revalidate' => [(new CacheHeaderBuilder())->withMustRevalidate()],
    'proxy revalidate' => [(new CacheHeaderBuilder())->withProxyRevalidate()],
    'must understand' => [(new CacheHeaderBuilder())->withMustUnderstand()],
    'immutable' => [(new CacheHeaderBuilder())->withImmutable()],
    'no transform' => [(new CacheHeaderBuilder())->withNoTransform()],
    'stale while revalidate' => [(new CacheHeaderBuilder())->withStaleWhileRevalidate(3600)],
    'stale if error' => [(new CacheHeaderBuilder())->withStaleIfError(3600)],
    'expires' => [(new CacheHeaderBuilder())->withExpires('Sun, 05 Sep 2021 00:00:00 GMT')],
    'etag' => [(new CacheHeaderBuilder())->withETag((new ETagHeaderBuilder())->withETag('123456'))],
    'age' => [(new CacheHeaderBuilder())->withAge(1)],
    'shared max age' => [(new CacheHeaderBuilder())->withSharedMaxAge(3600)],
    'max age' => [(new CacheHeaderBuilder())->withMaxAge(3600)],
    'last modified' => [(new CacheHeaderBuilder())->withLastModified(1)],
]);

it('with reset', function (CacheHeaderBuilder $builder) {
    expect($builder->toHeaders())->not()->toBeEmpty()
        ->and($builder->withReset()->toHeaders())->toBeEmpty()
        ->and($builder->toHeaders())->not()->toBeEmpty();
})->with([
    'no cache' => [(new CacheHeaderBuilder())->withNoCache()],
    'private' => [(new CacheHeaderBuilder())->withPrivate()],
    'public' => [(new CacheHeaderBuilder())->withPublic()],
    'no store' => [(new CacheHeaderBuilder())->withNoStore()],
    'must revalidate' => [(new CacheHeaderBuilder())->withMustRevalidate()],
    'proxy revalidate' => [(new CacheHeaderBuilder())->withProxyRevalidate()],
    'must understand' => [(new CacheHeaderBuilder())->withMustUnderstand()],
    'immutable' => [(new CacheHeaderBuilder())->withImmutable()],
    'no transform' => [(new CacheHeaderBuilder())->withNoTransform()],
    'stale while revalidate' => [(new CacheHeaderBuilder())->withStaleWhileRevalidate(3600)],
    'stale if error' => [(new CacheHeaderBuilder())->withStaleIfError(3600)],
    'expires' => [(new CacheHeaderBuilder())->withExpires('Sun, 05 Sep 2021 00:00:00 GMT')],
    'etag' => [(new CacheHeaderBuilder())->withETag((new ETagHeaderBuilder())->withETag('123456'))],
    'age' => [(new CacheHeaderBuilder())->withAge(1)],
    'shared max age' => [(new CacheHeaderBuilder())->withSharedMaxAge(3600)],
    'max age' => [(new CacheHeaderBuilder())->withMaxAge(3600)],
    'last modified' => [(new CacheHeaderBuilder())->withLastModified(1)],
]);

it('mixed', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    foreach ($builder->toHeaders() as $name => $value) {
        expect($expectedHeaders[$name])
            ->toEqualCanonicalizing(preg_split('!\s*,\s*!', $value));
    }
})->with([
    'public maxage=3600' => [
        (new CacheHeaderBuilder())
            ->withPublic()
            ->withMaxAge(3600),
        ['cache-control' => ['public', 'max-age=3600']],
    ],
    'public notransform smaxage=100 maxage=200' => [
        (new CacheHeaderBuilder())
            ->withPublic()
            ->withNoTransform()
            ->withSharedMaxAge(100)
            ->withMaxAge(200),
        ['cache-control' => ['public', 'no-transform', 's-maxage=100', 'max-age=200']],
    ],
    'mustrevalidate nostore' => [
        (new CacheHeaderBuilder())
            ->withMustRevalidate()
            ->withNoStore(),
        ['cache-control' => ['must-revalidate', 'no-store']],
    ],
    'public notransform smaxage=100 maxage=200 stalewhile=300 staleiferror=400' => [
        (new CacheHeaderBuilder())
            ->withPublic()
            ->withNoTransform()
            ->withSharedMaxAge(100)
            ->withMaxAge(200)
            ->withStaleWhileRevalidate(300)
            ->withStaleIfError(400),
        ['cache-control' => ['public', 'no-transform', 's-maxage=100', 'max-age=200', 'stale-while-revalidate=300', 'stale-if-error=400']],
    ],
]);