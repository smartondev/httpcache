<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('no store', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->noStore()->toHeaders())->toBe(['cache-control' => 'no-store'])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'no-store']);
});

it('with no store', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withNoStore()->toHeaders())->toBe(['cache-control' => 'no-store'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset no store', function () {
    $builder = (new CacheHeaderBuilder())
        ->noStore();
    expect($builder->toHeaders())->toBe(['cache-control' => 'no-store']);
    $builder->resetNoStore();
    expect($builder->toHeaders())->toBe([]);
});

it('without no store', function () {
    $builder = (new CacheHeaderBuilder())
        ->noStore();
    expect($builder->toHeaders())->toBe(['cache-control' => 'no-store'])
        ->and($builder->withoutNoStore()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'no-store']);
});
