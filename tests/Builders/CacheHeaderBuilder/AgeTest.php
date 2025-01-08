<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('age', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->age(10)->toHeaders())->toBe(['age' => '10'])
        ->and($builder->toHeaders())->toBe(['age' => '10']);
});

it('with age', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withAge(10)->toHeaders())->toBe(['age' => '10'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset age', function () {
    $builder = (new CacheHeaderBuilder())
        ->age(10);
    expect($builder->toHeaders())->toBe(['age' => '10']);
    $builder->resetAge();
    expect($builder->toHeaders())->toBe([]);
});

it('without age', function () {
    $builder = (new CacheHeaderBuilder())
        ->age(10);
    expect($builder->toHeaders())->toBe(['age' => '10'])
        ->and($builder->withoutAge()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['age' => '10']);
});
