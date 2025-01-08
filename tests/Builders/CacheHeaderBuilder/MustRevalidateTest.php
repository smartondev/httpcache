<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('must revalidate', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->mustRevalidate()->toHeaders())->toBe(['cache-control' => 'must-revalidate'])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'must-revalidate']);
});

it('with must revalidate', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withMustRevalidate()->toHeaders())->toBe(['cache-control' => 'must-revalidate'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset must revalidate', function () {
    $builder = (new CacheHeaderBuilder())
        ->mustRevalidate();
    expect($builder->toHeaders())->toBe(['cache-control' => 'must-revalidate']);
    $builder->resetMustRevalidate();
    expect($builder->toHeaders())->toBe([]);
});

it('without must revalidate', function () {
    $builder = (new CacheHeaderBuilder())
        ->mustRevalidate();
    expect($builder->toHeaders())->toBe(['cache-control' => 'must-revalidate'])
        ->and($builder->withoutMustRevalidate()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'must-revalidate']);
});
