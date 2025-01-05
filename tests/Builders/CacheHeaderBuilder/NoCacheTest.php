<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('no cache', function () {
    $noCacheExpectedHeaders = [
        'cache-control' => 'must-revalidate, no-cache, no-store, private',
        'pragma' => 'no-cache',
    ];
    $builderNoCache = (new CacheHeaderBuilder())
        ->noCache();
    expect($builderNoCache->toHeaders())->toBe($noCacheExpectedHeaders);
    $builder = $builderNoCache->withSharedMaxAge(60);
    expect($builder->toHeaders())->not()->toBe($noCacheExpectedHeaders)
        ->and($builder->toHeaders()['cache-control'])->not()->toContain('no-cache')
        ->and($builderNoCache->toHeaders())->not()->toBe($builder->toHeaders());
});

it('with no cache', function () {
    $noCacheExpectedHeaders = [
        'cache-control' => 'must-revalidate, no-cache, no-store, private',
        'pragma' => 'no-cache',
    ];
    $builderNoCache = (new CacheHeaderBuilder())
        ->withNoCache();
    expect($builderNoCache->toHeaders())->toBe($noCacheExpectedHeaders);
    $builder = $builderNoCache->withSharedMaxAge(60);
    expect($builder->toHeaders())->not()->toBe($noCacheExpectedHeaders)
        ->and($builder->toHeaders()['cache-control'])->not()->toContain('no-cache')
        ->and($builderNoCache->toHeaders())->not()->toBe($builder->toHeaders());
});

it('is no cache', function () {
    $builder = (new CacheHeaderBuilder())
        ->noCache();
    expect($builder->isNoCache())->toBeTrue()
        ->and($builder->withSharedMaxAge(60)->isNoCache())->toBeFalse();
});