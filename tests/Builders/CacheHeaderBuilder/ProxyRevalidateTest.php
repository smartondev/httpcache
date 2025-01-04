<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('proxy revalidate', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->proxyRevalidate()->toHeaders())->toBe(['cache-control' => 'proxy-revalidate'])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'proxy-revalidate']);
});

it('with proxy revalidate', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withProxyRevalidate()->toHeaders())->toBe(['cache-control' => 'proxy-revalidate'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset proxy revalidate', function () {
    $builder = (new CacheHeaderBuilder())
        ->proxyRevalidate();
    expect($builder->toHeaders())->toBe(['cache-control' => 'proxy-revalidate']);
    $builder->resetProxyRevalidate();
    expect($builder->toHeaders())->toBe([]);
});

it('without proxy revalidate', function () {
    $builder = (new CacheHeaderBuilder())
        ->proxyRevalidate();
    expect($builder->toHeaders())->toBe(['cache-control' => 'proxy-revalidate'])
        ->and($builder->withoutProxyRevalidate()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'proxy-revalidate']);
});
