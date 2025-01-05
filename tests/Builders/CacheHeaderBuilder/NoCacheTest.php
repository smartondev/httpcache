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

it('is no cache multiple stage', function() {
    $builder = (new CacheHeaderBuilder());
    expect($builder->isNoCache())->toBeFalse();
    $builder->noCache();
    expect($builder->isNoCache())->toBeTrue();
    $builder->reset();
    expect($builder->isNoCache())->toBeFalse();

    $builder->noCache();
    $builder->public();
    expect($builder->isNoCache())->toBeFalse();
});

it('no cache reset', function () {
    $builder = (new CacheHeaderBuilder())
        ->noCache();
    expect($builder->withReset()->toHeaders())->toBeEmpty()
        ->and($builder->withPrivate()->toHeaders())->toBe(['cache-control' => 'private'])
        ->and($builder->withPublic()->toHeaders())->toBe(['cache-control' => 'public'])
        ->and($builder->withNoStore()->toHeaders())->toBe(['cache-control' => 'no-store'])
        ->and($builder->withMustRevalidate()->toHeaders())->toBe(['cache-control' => 'must-revalidate']);
});
